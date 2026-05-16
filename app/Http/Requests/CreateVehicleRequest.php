<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreateVehicleRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'client_id'          => 'required|exists:clients,id',
            'marca'              => 'required|string',
            'modelo'             => 'required|string',
            'anio'               => 'required|integer|min:1900',
            'placas'             => 'nullable|string|unique:vehicles,placas',
            'vin'                => 'nullable|string|unique:vehicles,vin',
            'kilometraje_actual' => 'nullable|integer|min:0',
        ];
    }
}
