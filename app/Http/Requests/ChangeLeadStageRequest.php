<?php

namespace App\Http\Requests;

use App\Enums\LeadStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeLeadStageRequest extends FormRequest
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
            'stage' => [
                'required',
                Rule::enum(LeadStage::class),
            ],
        ];
    }
}
