<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class ClientResource extends JsonResource {
    public function toArray($request): array {
        $vehicles = $this->relationLoaded('vehicles')
            ? $this->vehicles->map(function ($vehicle) {
                $workOrders = $vehicle->relationLoaded('workOrders') ? $vehicle->workOrders : collect();
                $services = $workOrders
                    ->flatMap(fn ($workOrder) => $workOrder->relationLoaded('services') ? $workOrder->services : collect())
                    ->map(fn ($service) => $service->priceItem?->concepto ?? $service->nombre)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'id' => $vehicle->id,
                    'marca' => $vehicle->marca,
                    'modelo' => $vehicle->modelo,
                    'anio' => $vehicle->anio,
                    'placas' => $vehicle->placas,
                    'vin' => $vehicle->vin,
                    'services' => $services,
                    'workOrderIds' => $workOrders->pluck('id')->values()->all(),
                ];
            })->values()->all()
            : [];

        $workOrders = $this->relationLoaded('workOrders')
            ? $this->workOrders->map(function ($workOrder) {
                return [
                    'id' => $workOrder->id,
                    'status' => $workOrder->status,
                    'priority' => $workOrder->priority,
                    'fecha_programada' => $workOrder->fecha_programada,
                    'tecnico' => $workOrder->relationLoaded('tecnico') && $workOrder->tecnico ? ['name' => $workOrder->tecnico->name] : null,
                    'vehiculo' => $workOrder->relationLoaded('vehicle') && $workOrder->vehicle
                        ? [
                            'marca' => $workOrder->vehicle->marca,
                            'modelo' => $workOrder->vehicle->modelo,
                            'anio' => $workOrder->vehicle->anio,
                            'placas' => $workOrder->vehicle->placas,
                        ]
                        : null,
                    'paymentState' => $workOrder->relationLoaded('accountsReceivable') && $workOrder->accountsReceivable
                        ? $workOrder->accountsReceivable->estado_calculado
                        : 'Pendiente',
                ];
            })->values()->all()
            : [];

        return [
            'id'            => $this->id,
            'nombre'        => $this->nombre,
            'telefono'      => $this->telefono,
            'correo'        => $this->correo,
            'rfc'           => $this->rfc,
            'tag'           => $this->tag,
            'total_vehiculos' => $this->vehicles_count ?? ($this->relationLoaded('vehicles') ? $this->vehicles->count() : 0),
            'total_ots'      => $this->work_orders_count ?? ($this->relationLoaded('workOrders') ? $this->workOrders->count() : 0),
            'vehicles'      => $vehicles,
            'workOrders'    => $workOrders,
            'deleted_at'    => $this->deleted_at,
            'created_at'    => $this->created_at,
        ];
    }
}
