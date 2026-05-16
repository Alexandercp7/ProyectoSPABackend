<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class RecordCashEntryRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'fecha'       => 'required|date',
            'concepto'    => 'required|string',
            'tipo'        => 'required|in:Ingreso,Egreso',
            'monto'       => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:Efectivo,Transferencia,Tarjeta,Cheque',
            'referencia'  => 'nullable|string',
        ];
    }
}
