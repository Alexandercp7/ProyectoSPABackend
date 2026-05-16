<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AssignServiceRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['price_item_id' => 'required|exists:price_items,id'];
    }
}
