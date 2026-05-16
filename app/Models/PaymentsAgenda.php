<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaymentsAgenda extends Model
{
    protected $table = 'payments_agenda';
    protected $fillable = ['concepto','tipo','categoria','fecha_vencimiento','monto_presupuestado','monto_pagado','comprobante_url','notas'];

    public function getEstadoAttribute(): string
    {
        if ($this->monto_pagado >= $this->monto_presupuestado) return 'Pagado';
        if (Carbon::now()->gt($this->fecha_vencimiento)) return 'Vencido';
        return 'Pendiente';
    }
}
