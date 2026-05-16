<?php
namespace App\UseCases\WorkOrders;
use App\Models\ClientPaymentState;
use App\Models\WorkOrder;

class UpdateWorkOrderStatusUseCase
{
    public function execute(string $id, string $status, int $userId): WorkOrder
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->update(['status' => $status]);

        $wo->timeline()->create([
            'descripcion' => "Estado actualizado a: $status",
            'usuario_id'  => $userId,
        ]);

        return $wo->fresh();
    }
}
