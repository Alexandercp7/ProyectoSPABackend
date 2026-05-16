<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateWorkOrderStatusRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['status' => 'required|in:Agendado,En Espera,En Proceso,Terminado,En Garantia,Rezagado,Entregado'];
    }
}
