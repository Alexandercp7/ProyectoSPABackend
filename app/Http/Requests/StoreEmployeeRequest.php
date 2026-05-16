<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreEmployeeRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'nombre'   => 'required|string',
            'apellido' => 'required|string',
            'email'    => 'required|email|unique:employees,email',
            'role'     => 'required|string',
            'telefono' => 'nullable|string',
            'user_id'  => 'nullable|exists:users,id',
        ];
    }
}
