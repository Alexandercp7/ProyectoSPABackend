<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkOrderService extends Model
{
    protected $fillable = ['work_order_id','price_item_id','nombre','precio_auto','precio_camioneta','precio_camion'];
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function priceItem() { return $this->belongsTo(PriceItem::class); }
}
