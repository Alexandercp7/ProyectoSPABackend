<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['nombre','tipo','estado','responsable_id','stock_actual','stock_minimo','precio','precio_venta','linked_part_id','foto_url'];

    public function movements() { return $this->hasMany(InventoryMovement::class, 'item_id'); }
    public function linkedPart() { return $this->belongsTo(InventoryItem::class, 'linked_part_id'); }
    public function responsable() { return $this->belongsTo(User::class, 'responsable_id'); }

    public function getLowStockAttribute(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}
