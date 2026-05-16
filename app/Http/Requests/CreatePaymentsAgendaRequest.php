<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreatePaymentsAgendaRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'concepto'            => 'required|string',
            'tipo'                => 'required|in:ingreso,egreso',
            'categoria'           => 'required|string',
            'fecha_vencimiento'   => 'required|date',
            'monto_presupuestado' => 'required|numeric|min:0.01',
            'notas'               => 'nullable|string',
        ];
    }
}
