<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AssignPartRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'cantidad'          => 'required|integer|min:1',
        ];
    }
}
