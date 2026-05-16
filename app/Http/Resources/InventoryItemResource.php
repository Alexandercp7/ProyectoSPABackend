<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class InventoryItemResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'           => $this->id,
            'nombre'       => $this->nombre,
            'tipo'         => $this->tipo,
            'estado'       => $this->estado,
            'stock_actual' => $this->stock_actual,
            'stock_minimo' => $this->stock_minimo,
            'precio'       => $this->precio,
            'precio_venta' => $this->precio_venta,
            'low_stock'    => $this->low_stock,
            'foto_url'     => $this->foto_url,
            'created_at'   => $this->created_at,
        ];
    }
}
