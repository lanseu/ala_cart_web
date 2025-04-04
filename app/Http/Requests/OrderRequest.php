<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
            'currency_code' => 'required|string|size:3',
            'sub_total' => 'required|numeric',
            'discount_total' => 'required|numeric',
            'tax_total' => 'required|numeric',
            'shipping_total' => 'required|numeric',
            'total' => 'required|numeric',
            'customer_reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'lines' => 'required|array',
            'lines.*.product_id' => 'required|exists:lunar_products,id',
            'lines.*.description' => 'required|string',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.unit_price' => 'required|numeric',
            'lines.*.line_total' => 'required|numeric',
            'lines.*.tax_total' => 'required|numeric',
        ];
    }
}
