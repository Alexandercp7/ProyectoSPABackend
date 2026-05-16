<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkOrderPhoto extends Model
{
    protected $fillable = ['work_order_id','url','storage_path'];
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
}
