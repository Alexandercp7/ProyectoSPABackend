<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CreateWorkOrderRequest extends FormRequest {
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            // Option A: provide existing IDs
            'client_id'           => 'nullable|exists:clients,id',
            'vehicle_id'          => 'nullable|exists:vehicles,id',

            // Option B: provide raw client data (service will upsert)
            'cliente_nombre'      => 'nullable|string',
            'cliente_telefono'    => 'nullable|string',
            'cliente_correo'      => 'nullable|email|nullable',

            // Option B: raw vehicle data
            'vehiculo_marca'      => 'nullable|string',
            'vehiculo_modelo'     => 'nullable|string',
            'vehiculo_anio'       => 'nullable|integer',
            'vehiculo_placas'     => 'nullable|string',
            'vehiculo_vin'        => 'nullable|string',
            'vehiculo_kilometraje'=> 'nullable|integer',

            'tipo_vehiculo'       => 'required|in:Auto,Camioneta,Camion',
            'problema'            => 'required|string',
            'fecha_ingreso'       => 'required|date',
            'tecnico_id'          => 'nullable|exists:users,id',
            'priority'            => 'nullable|in:Alta,Media,Baja',
            'fecha_programada'    => 'nullable|date',
            'diagnostico'         => 'nullable|string',
        ];
    }

    public function withValidator($validator): void {
        $validator->after(function ($v) {
            $hasIds  = $this->filled('client_id') && $this->filled('vehicle_id');
            $hasData = $this->filled('cliente_nombre') && $this->filled('vehiculo_marca');
            if (!$hasIds && !$hasData) {
                $v->errors()->add('client_id', 'Proporciona client_id+vehicle_id o datos crudos del cliente/vehículo.');
            }
        });
    }
}
