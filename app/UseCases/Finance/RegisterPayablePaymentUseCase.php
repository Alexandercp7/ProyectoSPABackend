<?php
namespace App\UseCases\Finance;
use App\Exceptions\PaymentExceedsBalanceException;
use App\Models\AccountsPayable;
use App\Models\DailyCashEntry;
use Illuminate\Support\Facades\DB;

class RegisterPayablePaymentUseCase
{
    public function execute(int $cxpId, float $monto, string $metodoPago, int $userId): AccountsPayable
    {
        return DB::transaction(function () use ($cxpId, $monto, $metodoPago, $userId) {
            $cxp = AccountsPayable::lockForUpdate()->findOrFail($cxpId);

            if ($monto > $cxp->monto_pendiente) {
                throw new PaymentExceedsBalanceException(
                    "El monto $monto supera el pendiente {$cxp->monto_pendiente}."
                );
            }

            $cxp->monto_pagado += $monto;
            $cxp->monto_pendiente -= $monto;
            $cxp->estado = $cxp->monto_pendiente <= 0 ? 'Pagado' : 'Parcial';
            $cxp->save();

            DailyCashEntry::create([
                'fecha'      => now()->toDateString(),
                'concepto'   => "Pago CxP #{$cxp->id} - {$cxp->concepto}",
                'tipo'       => 'Egreso',
                'monto'      => $monto,
                'metodo_pago'=> $metodoPago,
                'usuario_id' => $userId,
            ]);

            return $cxp->fresh();
        });
    }
}
