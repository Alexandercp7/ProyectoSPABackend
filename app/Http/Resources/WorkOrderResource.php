<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class WorkOrderResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'              => $this->id,
            'status'          => $this->status,
            'priority'        => $this->priority,
            'tipo_vehiculo'   => $this->tipo_vehiculo,
            'problema'        => $this->problema,
            'fecha_ingreso'   => $this->fecha_ingreso,
            'fecha_programada'=> $this->fecha_programada,
            'cargo_generado'  => $this->cargo_generado,
            'cliente'         => $this->whenLoaded('client', fn() => [
                'id'      => $this->client->id,
                'nombre'  => $this->client->nombre,
                'telefono'=> $this->client->telefono,
                'correo'  => $this->client->correo,
            ]),
            'vehiculo'        => $this->whenLoaded('vehicle', fn() => [
                'id'     => $this->vehicle->id,
                'marca'  => $this->vehicle->marca,
                'modelo' => $this->vehicle->modelo,
                'anio'   => $this->vehicle->anio,
                'placas' => $this->vehicle->placas,
                'vin'    => $this->vehicle->vin,
            ]),
            'tecnico'         => $this->whenLoaded('tecnico', fn() => $this->tecnico?->only(['id','name'])),
            'created_at'      => $this->created_at,
        ];
    }
}
