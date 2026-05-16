<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkOrderTimeline extends Model
{
    public $timestamps = false;
    protected $table = 'work_order_timeline';
    protected $fillable = ['work_order_id','descripcion','usuario_id'];

    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function user() { return $this->belongsTo(User::class, 'usuario_id'); }
}
