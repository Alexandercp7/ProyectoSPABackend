<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreateInventoryItemRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'nombre'        => 'required|string',
            'tipo'          => 'required|in:refaccion,herramienta,equipo,consumible',
            'stock_actual'  => 'required|integer|min:0',
            'stock_minimo'  => 'nullable|integer|min:0',
            'precio'        => 'nullable|numeric|min:0',
            'precio_venta'  => 'nullable|numeric|min:0',
            'responsable_id'=> 'nullable|exists:users,id',
        ];
    }
}
