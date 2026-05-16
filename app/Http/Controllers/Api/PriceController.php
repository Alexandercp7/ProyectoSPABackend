<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePriceItemRequest;
use App\Http\Resources\PriceItemResource;
use App\Models\PriceItem;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min((int) ($request->per_page ?? 50), 500);
        $items = PriceItem::where('activo', true)
            ->when($request->categoria, fn($q) => $q->where('categoria', $request->categoria))
            ->when($request->sistema, fn($q) => $q->where('sistema', $request->sistema))
            ->when($request->familia, fn($q) => $q->where('familia', $request->familia))
            ->when($request->search, fn($q) => $q->where('concepto','like',"%{$request->search}%"))
            ->orderBy('id')
            ->paginate($perPage);
        return PriceItemResource::collection($items);
    }

    public function store(StorePriceItemRequest $request)
    {
        $item = PriceItem::create($request->validated());
        return response()->json(['data' => new PriceItemResource($item)], 201);
    }

    public function update(Request $request, int $id)
    {
        $item = PriceItem::findOrFail($id);
        $item->update($request->only(['concepto','sistema','familia','precio_auto','precio_camioneta','precio_camion','precio','activo']));
        return response()->json(['data' => new PriceItemResource($item)]);
    }
}
