<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkOrderPart extends Model
{
    protected $fillable = ['work_order_id','inventory_item_id','nombre','cantidad','costo_unitario'];
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function inventoryItem() { return $this->belongsTo(InventoryItem::class); }
}
