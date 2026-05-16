<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccountsReceivable extends Model
{
    protected $table = 'accounts_receivable';
    protected $fillable = ['work_order_id','client_id','monto','monto_recibido','monto_pendiente','fecha_emision','fecha_vencimiento','estado'];

    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function cashEntries() { return $this->hasMany(DailyCashEntry::class); }

    public function getEstadoCalculadoAttribute(): string
    {
        if ($this->monto_pendiente <= 0) return 'Pagado';
        if (Carbon::now()->gt($this->fecha_vencimiento) && $this->monto_pendiente > 0) return 'Vencido';
        if ($this->monto_recibido > 0) return 'Parcial';
        return 'Pendiente';
    }
}
