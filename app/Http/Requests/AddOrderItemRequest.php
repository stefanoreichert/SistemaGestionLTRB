<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Seleccioná un producto.',
            'product_id.exists'   => 'El producto seleccionado no existe.',
            'quantity.required'   => 'La cantidad es obligatoria.',
            'quantity.min'        => 'La cantidad mínima es 1.',
            'quantity.max'        => 'La cantidad máxima es 99.',
        ];
    }
}
