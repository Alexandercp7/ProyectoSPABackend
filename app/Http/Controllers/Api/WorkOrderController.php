<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPartRequest;
use App\Http\Requests\AssignServiceRequest;
use App\Http\Requests\CreateWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderStatusRequest;
use App\Http\Resources\WorkOrderDetailResource;
use App\Http\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use App\Services\TwilioService;
use App\UseCases\WorkOrders\AssignPartToWorkOrderUseCase;
use App\UseCases\WorkOrders\AssignServiceToWorkOrderUseCase;
use App\UseCases\WorkOrders\CloseWorkOrderUseCase;
use App\UseCases\WorkOrders\CreateWorkOrderUseCase;
use App\UseCases\WorkOrders\UpdateWorkOrderStatusUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Manages the lifecycle of work orders and coordinates client notifications.
 */
class WorkOrderController extends Controller
{
    public function __construct(
        private CreateWorkOrderUseCase $createUC,
        private CloseWorkOrderUseCase $closeUC,
        private UpdateWorkOrderStatusUseCase $statusUC,
        private AssignPartToWorkOrderUseCase $partUC,
        private AssignServiceToWorkOrderUseCase $serviceUC,
        private TwilioService $twilio,
    ) {}

    public function index(Request $request)
    {
        $query = WorkOrder::with(['client','vehicle','tecnico'])
            ->when($request->status, fn($q) => $q->byStatus($request->status))
            ->when($request->tecnico_id, fn($q) => $q->byTecnico($request->tecnico_id))
            ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
            ->when($request->search, fn($q) => $q->withSearch($request->search))
            ->orderByDesc('created_at');

        return WorkOrderResource::collection($query->paginate(20));
    }

    public function store(CreateWorkOrderRequest $request)
    {
        $wo = $this->createUC->execute($request->validated(), $request->user()->id);
        $wo->load(['client','vehicle','tecnico','timeline']);
        $this->twilio->notifyCreated($wo);
        return response()->json(['data' => new WorkOrderDetailResource($wo)], 201);
    }

    public function show(string $id)
    {
        $wo = WorkOrder::with([
            'client','vehicle','tecnico','timeline.user',
            'checklists','photos','notes','parts.inventoryItem',
            'services.priceItem','accountsReceivable',
        ])->findOrFail($id);
        return response()->json(['data' => new WorkOrderDetailResource($wo)]);
    }

    public function close(string $id, Request $request)
    {
        $wo = $this->closeUC->execute($id, $request->user()->id);
        $wo->load(['accountsReceivable','timeline','client','vehicle']);
        $this->twilio->notifyDelivered($wo);
        return response()->json(['data' => new WorkOrderDetailResource($wo)]);
    }

    public function updateStatus(UpdateWorkOrderStatusRequest $request, string $id)
    {
        $wo = $this->statusUC->execute($id, $request->status, $request->user()->id);
        $wo->load(['client','vehicle']);
        $this->twilio->notifyStatusChanged($wo);
        return response()->json(['data' => new WorkOrderResource($wo)]);
    }

    public function notifyWhatsApp(string $id)
    {
        $wo = WorkOrder::with(['client', 'vehicle'])->findOrFail($id);

        if (!$wo->client || blank($wo->client->telefono)) {
            return response()->json(['message' => 'La OT no tiene teléfono de cliente para enviar WhatsApp.'], 422);
        }

        if (!$this->twilio->isEnabled()) {
            return response()->json([
                'message' => 'Twilio WhatsApp no está configurado en el servidor.',
            ], 503);
        }

        $sent = $this->twilio->notifyWorkOrderStatusOrDelivery($wo);

        if (!$sent) {
            return response()->json([
                'message' => 'No se pudo enviar el WhatsApp. Revisa el número del cliente y la configuración de Twilio Sandbox.',
            ], 502);
        }

        return response()->json(['message' => 'WhatsApp enviado correctamente.']);
    }

    public function updateClient(Request $request, string $id)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'nombre'    => 'nullable|string',
            'telefono'  => 'nullable|string',
            'correo'    => 'nullable|email|nullable',
        ]);
        $wo = WorkOrder::with('client')->findOrFail($id);

        // If raw data provided, update the existing client's info
        if ($request->filled('nombre') && $wo->client) {
            $wo->client->update(array_filter([
                'nombre'   => $request->nombre,
                'telefono' => $request->telefono,
                'correo'   => $request->correo,
            ]));
        }
        // If new client_id provided, switch it
        if ($request->filled('client_id')) {
            $wo->update(['client_id' => $request->client_id]);
        }
        return response()->json(['data' => new WorkOrderResource($wo->fresh()->load('client'))]);
    }

    public function updateVehicle(Request $request, string $id)
    {
        $request->validate([
            'vehicle_id'         => 'nullable|exists:vehicles,id',
            'placas'             => 'nullable|string',
            'vin'                => 'nullable|string',
            'kilometraje_actual' => 'nullable|integer',
            'marca'              => 'nullable|string',
            'modelo'             => 'nullable|string',
            'anio'               => 'nullable|integer',
        ]);
        $wo = WorkOrder::with('vehicle')->findOrFail($id);

        // Update existing vehicle's data
        if ($wo->vehicle && ($request->filled('placas') || $request->filled('marca'))) {
            $wo->vehicle->update(array_filter([
                'placas'             => $request->placas,
                'vin'                => $request->vin,
                'kilometraje_actual' => $request->kilometraje_actual,
                'marca'              => $request->marca,
                'modelo'             => $request->modelo,
                'anio'               => $request->anio,
            ], fn($v) => !is_null($v)));
        }
        if ($request->filled('vehicle_id')) {
            $wo->update(['vehicle_id' => $request->vehicle_id]);
        }
        return response()->json(['data' => new WorkOrderResource($wo->fresh()->load('vehicle'))]);
    }

    public function updateDiagnosis(Request $request, string $id)
    {
        $request->validate([
            'problema'    => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'tecnico_id'  => 'nullable|exists:users,id',
        ]);
        $wo = WorkOrder::findOrFail($id);
        $wo->update($request->only(['problema','diagnostico','tecnico_id']));
        return response()->json(['data' => new WorkOrderResource($wo)]);
    }

    public function addChecklist(Request $request, string $id)
    {
        $request->validate([
            'tipo'        => 'required|in:inicial,trabajo',
            'tarea'       => 'required|string',
            'responsable' => 'nullable|string',
            'orden'       => 'nullable|integer',
        ]);
        $wo = WorkOrder::findOrFail($id);
        $item = $wo->checklists()->create($request->only(['tipo','tarea','responsable','orden']));
        return response()->json(['data' => $item], 201);
    }

    public function toggleChecklist(Request $request, string $id, int $item)
    {
        $wo = WorkOrder::findOrFail($id);
        $checklist = $wo->checklists()->findOrFail($item);
        $checklist->update(['completada' => !$checklist->completada]);

        $wo->timeline()->create([
            'descripcion' => "Checklist '{$checklist->tarea}': " . ($checklist->completada ? 'completado' : 'pendiente'),
            'usuario_id'  => $request->user()->id,
        ]);

        return response()->json(['data' => $checklist]);
    }

    public function uploadPhoto(Request $request, string $id)
    {
        $request->validate(['foto' => 'required|image|max:10240']);
        $wo = WorkOrder::findOrFail($id);

        $path = $request->file('foto')->store("work_orders/$id/photos", 'public');
        $url  = Storage::url($path);

        $photo = $wo->photos()->create(['url' => $url, 'storage_path' => $path]);
        return response()->json(['data' => $photo], 201);
    }

    public function addNote(Request $request, string $id)
    {
        $request->validate([
            'tipo'  => 'required|in:interna,cliente',
            'texto' => 'required|string',
        ]);
        $wo   = WorkOrder::findOrFail($id);
        $note = $wo->notes()->create([
            'tipo'       => $request->tipo,
            'texto'      => $request->texto,
            'usuario_id' => $request->user()->id,
        ]);
        return response()->json(['data' => $note], 201);
    }

    public function addPart(AssignPartRequest $request, string $id)
    {
        WorkOrder::findOrFail($id);
        $part = $this->partUC->execute($id, $request->inventory_item_id, $request->cantidad, $request->user()->id);
        return response()->json(['data' => $part], 201);
    }

    public function addService(AssignServiceRequest $request, string $id)
    {
        WorkOrder::findOrFail($id);
        $service = $this->serviceUC->execute($id, $request->price_item_id, $request->user()->id);
        return response()->json(['data' => $service], 201);
    }

    public function destroy(Request $request, string $id)
    {
        $request->validate(['confirm_id' => 'required|string']);
        if ($request->confirm_id !== $id) {
            return response()->json(['message' => 'El ID de confirmacion no coincide.'], 422);
        }
        $wo = WorkOrder::findOrFail($id);
        $wo->delete();
        return response()->json(['message' => 'Orden de trabajo eliminada.']);
    }
}
