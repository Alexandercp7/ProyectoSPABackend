<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AdjustStockRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'tipo'     => 'required|in:entrada,ajuste',
            'cantidad' => 'required|integer',
            'motivo'   => 'required|string',
        ];
    }
}
