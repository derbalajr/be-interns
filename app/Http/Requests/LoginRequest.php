<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ];
    }
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'The user\'s email address.',
                'example' => 'salma@example.com',
            ],

            'password' => [
                'description' => 'The user\'s password.',
                'example' => 'password123',
            ],
        ];
    }
}
