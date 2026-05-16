<?php
namespace App\UseCases\WorkOrders;
use App\Events\WorkOrderClosedEvent;
use App\Exceptions\AlreadyClosedException;
use App\Models\AccountsReceivable;
use App\Models\ClientPaymentState;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

class CloseWorkOrderUseCase
{
    public function execute(string $id, int $userId): WorkOrder
    {
        $wo = DB::transaction(function () use ($id, $userId) {
            $wo = WorkOrder::with(['parts','services'])->lockForUpdate()->findOrFail($id);
            throw_if($wo->cargo_generado, AlreadyClosedException::class);

            $total = $this->calculateTotal($wo);

            $wo->update(['status' => 'Terminado', 'cargo_generado' => true]);

            $cxc = AccountsReceivable::create([
                'work_order_id'    => $wo->id,
                'client_id'        => $wo->client_id,
                'monto'            => $total,
                'monto_pendiente'  => $total,
                'fecha_emision'    => now()->toDateString(),
                'fecha_vencimiento'=> now()->addDays(30)->toDateString(),
                'estado'           => 'Pendiente',
            ]);

            ClientPaymentState::updateOrCreate(
                ['client_id' => $wo->client_id, 'work_order_id' => $wo->id],
                ['estado' => 'Pendiente']
            );

            $wo->timeline()->create([
                'descripcion' => "OT cerrada. CxC #{$cxc->id} generada por $" . number_format($total, 2),
                'usuario_id'  => $userId,
            ]);

            return $wo->fresh();
        });

        event(new WorkOrderClosedEvent($wo));

        return $wo;
    }

    private function calculateTotal(WorkOrder $wo): float
    {
        $parts = $wo->parts->sum(fn($p) => $p->cantidad * $p->costo_unitario);

        $services = $wo->services->sum(function ($s) use ($wo) {
            return match ($wo->tipo_vehiculo) {
                'camioneta' => $s->precio_camioneta,
                'camion'    => $s->precio_camion,
                default     => $s->precio_auto,
            };
        });

        return $parts + $services;
    }
}
