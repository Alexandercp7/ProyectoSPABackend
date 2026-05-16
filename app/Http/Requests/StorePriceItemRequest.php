<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StorePriceItemRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'categoria'          => 'required|in:automotriz,torno',
            'categoria_principal'=> 'nullable|string',
            'sistema'            => 'required|string',
            'familia'            => 'nullable|string',
            'concepto'           => 'required|string',
            'tamano'             => 'nullable|string',
            'diametro'           => 'nullable|string',
            'precio_auto'        => 'nullable|numeric|min:0',
            'precio_camioneta'   => 'nullable|numeric|min:0',
            'precio_camion'      => 'nullable|numeric|min:0',
            'precio'             => 'nullable|numeric|min:0',
        ];
    }
}
