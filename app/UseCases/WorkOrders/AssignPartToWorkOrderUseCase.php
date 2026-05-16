<?php
namespace App\UseCases\WorkOrders;
use App\Exceptions\InsufficientStockException;
use App\Jobs\LowStockAlertJob;
use App\Models\InventoryItem;
use App\Models\WorkOrderPart;
use Illuminate\Support\Facades\DB;

class AssignPartToWorkOrderUseCase
{
    public function execute(string $workOrderId, int $itemId, int $cantidad, int $userId): WorkOrderPart
    {
        return DB::transaction(function () use ($workOrderId, $itemId, $cantidad, $userId) {
            $item = InventoryItem::lockForUpdate()->findOrFail($itemId);

            if ($item->stock_actual < $cantidad) {
                throw new InsufficientStockException(
                    "Stock insuficiente. Disponible: {$item->stock_actual}, solicitado: {$cantidad}."
                );
            }

            $part = WorkOrderPart::create([
                'work_order_id'    => $workOrderId,
                'inventory_item_id'=> $itemId,
                'nombre'           => $item->nombre,
                'cantidad'         => $cantidad,
                'costo_unitario'   => $item->precio,
            ]);

            $nuevoStock = $item->stock_actual - $cantidad;
            $item->update(['stock_actual' => $nuevoStock]);

            $item->movements()->create([
                'tipo'            => 'salida_ot',
                'cantidad'        => $cantidad,
                'stock_resultante'=> $nuevoStock,
                'work_order_id'   => $workOrderId,
                'motivo'          => "Asignado a OT $workOrderId",
                'usuario_id'      => $userId,
            ]);

            if ($nuevoStock <= $item->stock_minimo) {
                LowStockAlertJob::dispatch($item);
            }

            return $part;
        });
    }
}
