<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class PriceItemResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'                => $this->id,
            'categoria'         => $this->categoria,
            'categoria_principal'=> $this->categoria_principal,
            'sistema'           => $this->sistema,
            'familia'          => $this->familia,
            'concepto'         => $this->concepto,
            'tamano'           => $this->tamano,
            'diametro'         => $this->diametro,
            'precio_auto'      => $this->precio_auto,
            'precio_camioneta' => $this->precio_camioneta,
            'precio_camion'    => $this->precio_camion,
            'precio'           => $this->precio,
            'activo'           => $this->activo,
        ];
    }
}
