<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    public $timestamps = false;
    protected $fillable = ['item_id','tipo','cantidad','stock_resultante','work_order_id','motivo','usuario_id'];

    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function user() { return $this->belongsTo(User::class, 'usuario_id'); }
}
