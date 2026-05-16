<?php
namespace App\UseCases\Finance;
use App\Exceptions\PaymentExceedsBalanceException;
use App\Models\AccountsReceivable;
use App\Models\ClientPaymentState;
use App\Models\DailyCashEntry;
use Illuminate\Support\Facades\DB;

class RegisterReceivablePaymentUseCase
{
    public function execute(int $cxcId, float $monto, string $metodoPago, int $userId): AccountsReceivable
    {
        return DB::transaction(function () use ($cxcId, $monto, $metodoPago, $userId) {
            $cxc = AccountsReceivable::lockForUpdate()->findOrFail($cxcId);

            if ($monto > $cxc->monto_pendiente) {
                throw new PaymentExceedsBalanceException(
                    "El monto $monto supera el pendiente {$cxc->monto_pendiente}."
                );
            }

            $cxc->monto_recibido += $monto;
            $cxc->monto_pendiente -= $monto;
            $cxc->estado = $cxc->monto_pendiente <= 0 ? 'Pagado' : 'Parcial';
            $cxc->save();

            DailyCashEntry::create([
                'fecha'                   => now()->toDateString(),
                'concepto'                => "Pago CxC #{$cxc->id}",
                'tipo'                    => 'Ingreso',
                'monto'                   => $monto,
                'metodo_pago'             => $metodoPago,
                'accounts_receivable_id'  => $cxc->id,
                'usuario_id'              => $userId,
            ]);

            ClientPaymentState::updateOrCreate(
                ['client_id' => $cxc->client_id, 'work_order_id' => $cxc->work_order_id],
                ['estado' => $cxc->estado === 'Pagado' ? 'Pagado' : 'Pendiente']
            );

            return $cxc->fresh();
        });
    }
}
