<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'max_price' => [
                'sometimes',
                'numeric',
                'min:0',

                Rule::when(
                    $this->filled('min_price'),
                    ['gte:min_price']
                ),
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    Unit::STATUS_AVAILABLE,
                    Unit::STATUS_SOLD,
                    Unit::STATUS_RESERVED,
                ]),
            ],

            'sort' => [
                'sometimes',
                Rule::in([
                    'price_asc',
                    'price_desc',
                    'oldest',
                    'latest',
                ]),
            ],
        ];
    }
}
