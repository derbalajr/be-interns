<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'source' => [
                'required',
                Rule::in([
                    'website',
                    'referral',
                    'social_media',
                    'phone_call',
                    'walk_in',
                ]),
            ],

            'budget' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'agent_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }
}
