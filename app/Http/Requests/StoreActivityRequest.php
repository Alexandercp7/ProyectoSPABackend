<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreActivityRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'titulo'        => 'required|string',
            'descripcion'   => 'nullable|string',
            'asignado_a_id' => 'nullable|exists:users,id',
            'fecha_limite'  => 'nullable|date',
            'prioridad'     => 'nullable|in:Alta,Media,Baja',
            'etiqueta'      => 'nullable|string',
            'estado'        => 'nullable|in:Pendiente,En progreso,Completada,Cancelada',
        ];
    }
}
