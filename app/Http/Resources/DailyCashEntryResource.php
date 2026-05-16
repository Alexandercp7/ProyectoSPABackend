<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class DailyCashEntryResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'          => $this->id,
            'fecha'       => $this->fecha,
            'concepto'    => $this->concepto,
            'tipo'        => $this->tipo,
            'monto'       => $this->monto,
            'metodo_pago' => $this->metodo_pago,
            'referencia'  => $this->referencia,
            'created_at'  => $this->created_at,
        ];
    }
}
