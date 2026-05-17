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
            'total_vehiculos' => $this->vehicles_count ?? ($this->relationLoaded('vehicles') ? $this->vehicles->count() : 0),
            'total_ots'      => $this->work_orders_count ?? ($this->relationLoaded('workOrders') ? $this->workOrders->count() : 0),
            'deleted_at'    => $this->deleted_at,
            'created_at'    => $this->created_at,
        ];
    }
}
