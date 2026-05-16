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

class InventoryController extends Controller
{
    public function __construct(
        private CreateInventoryItemUseCase $createUC,
        private AdjustInventoryStockUseCase $adjustUC,
    ) {}

    public function index(Request $request)
    {
        $items = InventoryItem::when($request->tipo, fn($q) => $q->where('tipo', $request->tipo))
            ->paginate(50);
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

    public function custodyIndex()
    {
        return response()->json(['data' => ClientCustody::with(['client','workOrder'])->where('estado','Resguardado')->get()]);
    }

    public function custodyStore(Request $request)
    {
        $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'client_id'     => 'required|exists:clients,id',
            'item'          => 'required|string',
            'responsable'   => 'required|string',
            'fecha_ingreso' => 'required|date',
        ]);
        $custody = ClientCustody::create($request->validated());
        return response()->json(['data' => $custody], 201);
    }

    public function custodyDeliver(int $id)
    {
        $custody = ClientCustody::findOrFail($id);
        $custody->update(['estado' => 'Entregado']);
        return response()->json(['data' => $custody]);
    }
}
