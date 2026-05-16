<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccountsPayable extends Model
{
    protected $table = 'accounts_payable';
    protected $fillable = ['contact_id','concepto','monto','monto_pagado','monto_pendiente','fecha_vencimiento','estado'];

    public function contact() { return $this->belongsTo(Contact::class); }

    public function getEstadoCalculadoAttribute(): string
    {
        if ($this->monto_pendiente <= 0) return 'Pagado';
        if (Carbon::now()->gt($this->fecha_vencimiento) && $this->monto_pendiente > 0) return 'Vencido';
        if ($this->monto_pagado > 0) return 'Parcial';
        return 'Pendiente';
    }
}
