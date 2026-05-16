<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class PaymentsAgendaResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'                  => $this->id,
            'concepto'            => $this->concepto,
            'tipo'                => $this->tipo,
            'categoria'           => $this->categoria,
            'fecha_vencimiento'   => $this->fecha_vencimiento,
            'monto_presupuestado' => $this->monto_presupuestado,
            'monto_pagado'        => $this->monto_pagado,
            'estado'              => $this->estado,
            'comprobante_url'     => $this->comprobante_url,
            'notas'               => $this->notas,
            'created_at'          => $this->created_at,
        ];
    }
}
