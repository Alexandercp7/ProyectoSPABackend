<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class WorkOrderDetailResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'              => $this->id,
            'status'          => $this->status,
            'priority'        => $this->priority,
            'tipo_vehiculo'   => $this->tipo_vehiculo,
            'problema'        => $this->problema,
            'diagnostico'     => $this->diagnostico,
            'fecha_ingreso'   => $this->fecha_ingreso,
            'fecha_programada'=> $this->fecha_programada,
            'cargo_generado'  => $this->cargo_generado,
            'cliente'         => new ClientResource($this->whenLoaded('client')),
            'vehiculo'        => $this->whenLoaded('vehicle'),
            'tecnico'         => $this->whenLoaded('tecnico', fn() => $this->tecnico?->only(['id','name','email'])),
            'timeline'        => $this->whenLoaded('timeline'),
            'checklists'      => $this->whenLoaded('checklists'),
            'fotos'           => $this->whenLoaded('photos'),
            'notas'           => $this->whenLoaded('notes'),
            'partes'          => $this->whenLoaded('parts'),
            'servicios'       => $this->whenLoaded('services'),
            'cxc'             => $this->whenLoaded('accountsReceivable', fn() =>
                $this->accountsReceivable ? new AccountsReceivableResource($this->accountsReceivable) : null
            ),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
