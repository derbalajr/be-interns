<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDealRequest extends FormRequest
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
            'lead_id' => [
                'required',
                'integer',
                'exists:leads,id',
            ],

            'unit_id' => [
                'nullable',
                'integer',
            ],

            'agent_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'stage' => [
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

            'value' => [
                'required',
                'numeric',
                'min:0',
            ],

            'expected_close' => [
                'nullable',
                'date',
            ],
        ];
    }
}
