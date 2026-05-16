<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class AccountsReceivableResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'               => $this->id,
            'work_order_id'    => $this->work_order_id,
            'client_id'        => $this->client_id,
            'cliente'          => $this->whenLoaded('client', fn() => $this->client->nombre),
            'monto'            => $this->monto,
            'monto_recibido'   => $this->monto_recibido,
            'monto_pendiente'  => $this->monto_pendiente,
            'fecha_emision'    => $this->fecha_emision,
            'fecha_vencimiento'=> $this->fecha_vencimiento,
            'estado'           => $this->estado,
            'estado_calculado' => $this->estado_calculado,
            'created_at'       => $this->created_at,
        ];
    }
}
