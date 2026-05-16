<?php
namespace App\UseCases\WorkOrders;
use App\Models\PriceItem;
use App\Models\WorkOrderService;

class AssignServiceToWorkOrderUseCase
{
    public function execute(string $workOrderId, int $priceItemId, int $userId): WorkOrderService
    {
        $priceItem = PriceItem::findOrFail($priceItemId);

        $service = WorkOrderService::create([
            'work_order_id'   => $workOrderId,
            'price_item_id'   => $priceItemId,
            'nombre'          => $priceItem->concepto,
            'precio_auto'     => $priceItem->precio_auto,
            'precio_camioneta'=> $priceItem->precio_camioneta,
            'precio_camion'   => $priceItem->precio_camion,
        ]);

        return $service;
    }
}
