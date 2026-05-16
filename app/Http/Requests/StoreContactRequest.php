<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreContactRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'nombre'              => 'required|string',
            'telefono'            => 'required|string',
            'rfc'                 => 'nullable|string',
            'empresa'             => 'nullable|string',
            'correo'              => 'nullable|email',
            'dias_pago'           => 'nullable|integer|min:0',
            'limite_credito'      => 'nullable|numeric|min:0',
            'politica_descuentos' => 'nullable|string',
        ];
    }
}
