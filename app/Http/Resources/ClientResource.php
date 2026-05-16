<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class ClientResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'            => $this->id,
            'nombre'        => $this->nombre,
            'telefono'      => $this->telefono,
            'correo'        => $this->correo,
            'rfc'           => $this->rfc,
            'tag'           => $this->tag,
            'total_vehiculos'=> $this->whenLoaded('vehicles', fn() => $this->vehicles->count()),
            'total_ots'     => $this->whenLoaded('workOrders', fn() => $this->workOrders->count()),
            'deleted_at'    => $this->deleted_at,
            'created_at'    => $this->created_at,
        ];
    }
}
