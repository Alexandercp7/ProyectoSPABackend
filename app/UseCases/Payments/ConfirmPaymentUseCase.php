<?php
namespace App\UseCases\Payments;
use App\Models\PaymentsAgenda;

class ConfirmPaymentUseCase
{
    public function execute(int $agendaId, int $userId): PaymentsAgenda
    {
        $agenda = PaymentsAgenda::findOrFail($agendaId);
        $agenda->update(['monto_pagado' => $agenda->monto_presupuestado]);
        return $agenda->fresh();
    }
}
