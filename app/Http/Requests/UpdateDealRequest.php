<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDealRequest extends FormRequest
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
                'sometimes',
                'integer',
                'exists:leads,id',
            ],

            'unit_id' => [
                'nullable',
                'integer',
                'exists:units,id',
            ],

            'agent_id' => [
                'sometimes',
                'integer',
                'exists:users,id',
            ],

            'stage' => [
                'sometimes',
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
                'sometimes',
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
