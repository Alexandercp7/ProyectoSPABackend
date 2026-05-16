<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class ActivityResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'          => $this->id,
            'titulo'      => $this->titulo,
            'descripcion' => $this->descripcion,
            'estado'      => $this->estado,
            'prioridad'   => $this->prioridad,
            'etiqueta'    => $this->etiqueta,
            'fecha_limite'=> $this->fecha_limite,
            'asignado_a'  => $this->whenLoaded('asignadoA', fn() => $this->asignadoA?->only(['id','name'])),
            'comentarios' => $this->whenLoaded('comments'),
            'created_at'  => $this->created_at,
        ];
    }
}
