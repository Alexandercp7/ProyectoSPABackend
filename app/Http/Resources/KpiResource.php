<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class KpiResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'               => $this->id,
            'nombre'           => $this->nombre,
            'descripcion'      => $this->descripcion,
            'role_responsable' => $this->role_responsable,
            'meta'             => $this->meta,
            'periodo'          => $this->periodo,
            'fecha_inicio'     => $this->fecha_inicio,
            'fecha_fin'        => $this->fecha_fin,
            'progreso'         => $this->progreso,
        ];
    }
}
