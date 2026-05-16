<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkOrderChecklist extends Model
{
    protected $fillable = ['work_order_id','tipo','tarea','responsable','completada','orden'];
    protected $casts = ['completada' => 'boolean'];
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
}
