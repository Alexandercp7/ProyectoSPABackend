<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['work_order_id','cliente','satisfaccion_general','calidad_trabajo','trato_recibido','recomendacion','comentarios','token','fecha'];
    protected $casts = ['recomendacion' => 'boolean'];
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
}
