<?php
namespace App\UseCases\Inventory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class AdjustInventoryStockUseCase
{
    public function execute(int $itemId, string $tipo, int $cantidad, string $motivo, int $userId): InventoryItem
    {
        return DB::transaction(function () use ($itemId, $tipo, $cantidad, $motivo, $userId) {
            $item = InventoryItem::lockForUpdate()->findOrFail($itemId);

            $nuevoStock = $tipo === 'entrada'
                ? $item->stock_actual + $cantidad
                : $item->stock_actual + $cantidad; // for ajuste, cantidad can be negative

            if ($tipo === 'ajuste') {
                $nuevoStock = $item->stock_actual + $cantidad;
            } elseif ($tipo === 'entrada') {
                $nuevoStock = $item->stock_actual + abs($cantidad);
            }

            $item->update(['stock_actual' => max(0, $nuevoStock)]);

            $item->movements()->create([
                'tipo'            => $tipo,
                'cantidad'        => $cantidad,
                'stock_resultante'=> $item->stock_actual,
                'motivo'          => $motivo,
                'usuario_id'      => $userId,
            ]);

            return $item->fresh();
        });
    }
}
