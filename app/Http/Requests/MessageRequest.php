<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:conversation,promotion',
            'name' => 'required|string|max:255',
            'chat' => 'required|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
