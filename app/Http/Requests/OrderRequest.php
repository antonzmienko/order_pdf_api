<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'products' => json_decode($this->products, 1),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'supplier_title' => 'required|max:255',
            'supplier_inn' => 'required|max:30',
            'supplier_kpp' => 'required|max:30',
            'supplier_address' => 'required|max:255',
            'client_fio' => 'required|max:255',
            'client_inn' => 'required|max:30',
            'client_address' => 'required|max:255',
            'logo' => 'required|image',
            'total_quantity' => 'required|numeric',
            'total_sum' => 'required|numeric',
            'order_number' => 'required|max:30',
            'products' => 'required|array',
            'products.*.name' => 'required|max:255',
            'products.*.quantity' => 'required|numeric|gt:0',
            'products.*.unit' => 'required',
            'products.*.price' => 'required|numeric|gte:0',
            'products.*.sum' => 'required|numeric|gte:0',
        ];
    }
}
