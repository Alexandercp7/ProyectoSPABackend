<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\CreateInventoryItemRequest;
use App\Http\Resources\InventoryItemResource;
use App\Models\ClientCustody;
use App\Models\InventoryItem;
use App\UseCases\Inventory\AdjustInventoryStockUseCase;
use App\UseCases\Inventory\CreateInventoryItemUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Handles inventory items, stock movements, and client custody records.
 */
class InventoryController extends Controller
{
    public function __construct(
        private CreateInventoryItemUseCase $createUC,
        private AdjustInventoryStockUseCase $adjustUC,
    ) {}

    public function index(Request $request)
    {
        $items = InventoryItem::when($request->tipo, fn($q) => $q->where('tipo', $request->tipo))
            ->paginate((int) ($request->per_page ?? 50));
        return InventoryItemResource::collection($items);
    }

    public function store(CreateInventoryItemRequest $request)
    {
        $item = $this->createUC->execute($request->validated(), $request->user()->id);
        return response()->json(['data' => new InventoryItemResource($item)], 201);
    }

    public function update(Request $request, int $id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->update($request->only(['nombre','estado','precio','precio_venta','stock_minimo','responsable_id']));
        return response()->json(['data' => new InventoryItemResource($item)]);
    }

    public function movements(int $id)
    {
        $item      = InventoryItem::findOrFail($id);
        $movements = $item->movements()->orderByDesc('created_at')->paginate(30);
        return response()->json(['data' => $movements]);
    }

    public function addMovement(AdjustStockRequest $request, int $id)
    {
        $item = $this->adjustUC->execute($id, $request->tipo, $request->cantidad, $request->motivo, $request->user()->id);
        return response()->json(['data' => new InventoryItemResource($item)]);
    }

    public function uploadPhoto(Request $request, int $id)
    {
        $request->validate(['foto' => 'required|image|max:5120']);
        $item = InventoryItem::findOrFail($id);

        if ($item->foto_url) {
            $oldPath = preg_replace('#^.*/storage/#', '', $item->foto_url);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('foto')->store("inventory/$id", 'public');
        $url  = $request->getSchemeAndHttpHost() . Storage::url($path);
        $item->update(['foto_url' => $url]);

        return response()->json(['data' => new InventoryItemResource($item)]);
    }

    public function custodyIndex()
    {
        return response()->json(['data' => ClientCustody::with(['client','workOrder'])->where('estado','Resguardado')->get()]);
    }

    public function custodyStore(Request $request)
    {
        $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'item'          => 'required|string',
            'responsable'   => 'required|string',
            'fecha_ingreso' => 'required|date',
        ]);

        $workOrder = \App\Models\WorkOrder::findOrFail($request->work_order_id);
        $custody = ClientCustody::create([
            'work_order_id' => $request->work_order_id,
            'client_id'     => $workOrder->client_id,
            'item'          => $request->item,
            'responsable'   => $request->responsable,
            'fecha_ingreso' => $request->fecha_ingreso,
        ]);
        return response()->json(['data' => $custody], 201);
    }

    public function custodyDeliver(int $id)
    {
        $custody = ClientCustody::findOrFail($id);
        $custody->update(['estado' => 'Entregado']);
        return response()->json(['data' => $custody]);
    }
}
