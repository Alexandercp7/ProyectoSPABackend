<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PriceItem extends Model
{
    protected $fillable = ['categoria','categoria_principal','sistema','familia','concepto','tamano','diametro','precio_auto','precio_camioneta','precio_camion','precio','activo'];
    protected $casts = ['activo' => 'boolean'];
    public function workOrderServices() { return $this->hasMany(WorkOrderService::class); }
}
