<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stage' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'q' => ['nullable', 'string'],

            'min_budget' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'max_budget' => [
                'nullable',
                'numeric',
                'gte:min_budget',
            ],

            'sort' => [
                'nullable',
                'in:name,created_at,-name,-created_at',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'between:1,100',
            ],
        ];
    }
}
