<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
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
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],

            'source' => [
                'sometimes',
                'required',
                Rule::in([
                    'website',
                    'referral',
                    'social_media',
                    'phone_call',
                    'walk_in',
                ]),
            ],

            'stage' => [
                'sometimes',
                'required',
                Rule::in([
                    'new',
                    'contacted',
                    'qualified',
                    'negotiation',
                    'won',
                    'lost',
                ]),
            ],

            'budget' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
            ],

            'agent_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:users,id',
            ],
        ];
    }
}
