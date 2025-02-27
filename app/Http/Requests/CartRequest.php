<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ensure user is authenticated via middleware
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:lunar_products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
