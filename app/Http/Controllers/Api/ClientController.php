<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\UseCases\Clients\DeleteClientUseCase;
use App\UseCases\Clients\UpsertClientUseCase;
use Illuminate\Http\Request;

/**
 * Handles client records and their associated vehicles and work orders.
 */
class ClientController extends Controller
{
    public function __construct(
        private UpsertClientUseCase $upsertUC,
        private DeleteClientUseCase $deleteUC,
    ) {}

    public function index(Request $request)
    {
        $clients = Client::withCount(['workOrders','vehicles'])
            ->with('paymentStates')
            ->when($request->search, function ($q) use ($request) {
                $q->where('nombre','like',"%{$request->search}%")
                  ->orWhere('telefono','like',"%{$request->search}%");
            })
            ->paginate(20);
        return ClientResource::collection($clients);
    }

    public function store(UpsertClientRequest $request)
    {
        $client = $this->upsertUC->execute($request->validated());
        return response()->json(['data' => new ClientResource($client)], 201);
    }

    public function show(int $id)
    {
        $client = Client::with(['vehicles','workOrders','paymentStates'])->findOrFail($id);
        $client = Client::with([
            'vehicles.workOrders.services.priceItem',
            'workOrders.vehicle',
            'workOrders.tecnico',
            'workOrders.accountsReceivable',
            'paymentStates',
        ])->findOrFail($id);
        return response()->json(['data' => new ClientResource($client)]);
    }

    public function update(UpsertClientRequest $request, int $id)
    {
        $client = $this->upsertUC->execute($request->validated(), $id);
        return response()->json(['data' => new ClientResource($client)]);
    }

    public function destroy(int $id)
    {
        $this->deleteUC->execute($id);
        return response()->json(['message' => 'Cliente eliminado correctamente.']);
    }
}
