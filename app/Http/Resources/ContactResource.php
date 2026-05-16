<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class ContactResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'                  => $this->id,
            'nombre'              => $this->nombre,
            'rfc'                 => $this->rfc,
            'empresa'             => $this->empresa,
            'telefono'            => $this->telefono,
            'correo'              => $this->correo,
            'dias_pago'           => $this->dias_pago,
            'limite_credito'      => $this->limite_credito,
            'politica_descuentos' => $this->politica_descuentos,
            'productos'           => $this->whenLoaded('products'),
            'etiquetas'           => $this->whenLoaded('tags', fn() => $this->tags->pluck('tag')),
            'created_at'          => $this->created_at,
        ];
    }
}
