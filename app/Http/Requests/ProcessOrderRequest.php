<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true; // or add authorization logic
    }

    public function rules()
    {
        return [
            'notes' => 'nullable|string|max:255',
        ];
    }
}

