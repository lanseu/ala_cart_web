<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users, you might want to check for authentication here
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:lunar_products,id',
            'variant_id' => 'required|exists:lunar_product_variants,id', // Ensure variant_id is required and exists in the variants table
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'variant_id.required' => 'The variant ID field is required.',
            'variant_id.exists' => 'The selected variant does not exist.',
        ];
    }
}
