<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $fillable = ['nombre','telefono','correo','rfc'];

    public function vehicles() { return $this->hasMany(Vehicle::class); }
    public function workOrders() { return $this->hasMany(WorkOrder::class); }
    public function paymentStates() { return $this->hasMany(ClientPaymentState::class); }

    public function getTagAttribute(): string
    {
        $states = $this->paymentStates()->pluck('estado');
        if ($states->isEmpty()) return 'Sin historial';
        if ($states->contains('Vencido')) return 'Vencido';
        if ($states->every(fn($s) => $s === 'Pagado')) return 'Al corriente';
        if ($states->contains('Pendiente')) return 'Pendiente';
        return 'Sin historial';
    }
}
