<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkOrder extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (WorkOrder $wo) {
            if (empty($wo->portal_token)) {
                $wo->portal_token = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'id','client_id','vehicle_id','tecnico_id','status','priority',
        'tipo_vehiculo','problema','diagnostico','fecha_ingreso',
        'fecha_programada','cargo_generado','portal_token',
    ];

    protected $casts = ['cargo_generado' => 'boolean', 'fecha_ingreso' => 'datetime'];

    public function client() { return $this->belongsTo(Client::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function tecnico() { return $this->belongsTo(User::class, 'tecnico_id'); }
    public function timeline() { return $this->hasMany(WorkOrderTimeline::class); }
    public function checklists() { return $this->hasMany(WorkOrderChecklist::class); }
    public function photos() { return $this->hasMany(WorkOrderPhoto::class); }
    public function notes() { return $this->hasMany(WorkOrderNote::class); }
    public function parts() { return $this->hasMany(WorkOrderPart::class); }
    public function services() { return $this->hasMany(WorkOrderService::class); }
    public function accountsReceivable() { return $this->hasOne(AccountsReceivable::class); }
    public function paymentState() { return $this->hasOne(ClientPaymentState::class); }

    public function scopeByStatus($q, $s) { return $q->where('status', $s); }
    public function scopeByTecnico($q, $id) { return $q->where('tecnico_id', $id); }
    public function scopeWithSearch($q, $term) {
        return $q->where(function($q) use ($term) {
            $q->where('id','like',"%$term%")
              ->orWhereHas('client', fn($q) => $q->where('nombre','like',"%$term%"))
              ->orWhere('problema','like',"%$term%");
        });
    }
}
