<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class AccountsPayableResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'               => $this->id,
            'concepto'         => $this->concepto,
            'contact_id'       => $this->contact_id,
            'proveedor'        => $this->whenLoaded('contact', fn() => $this->contact?->nombre),
            'monto'            => $this->monto,
            'monto_pagado'     => $this->monto_pagado,
            'monto_pendiente'  => $this->monto_pendiente,
            'fecha_vencimiento'=> $this->fecha_vencimiento,
            'estado'           => $this->estado,
            'estado_calculado' => $this->estado_calculado,
            'created_at'       => $this->created_at,
        ];
    }
}
