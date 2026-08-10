<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    /**
     * Only users with the create-units permission may create units.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create-units');
    }

    /**
     * Validate the submitted unit information.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:100',

                Rule::unique('units', 'code')
                    ->where(
                        fn ($query) => $query->where(
                            'project_id',
                            $this->input('project_id')
                        )
                    ),
            ],

            'type' => [
                'required',
                'string',
                'max:100',
            ],

            'area' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],

            'status' => [
                'prohibited',
            ],
            'photos' => [
                'sometimes',
                'array',
            ],

            'photos.*' => [
                'image',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:5120',
            ],

            'floor_plans' => [
                'sometimes',
                'array',
            ],

            'floor_plans.*' => [
                'image',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:5120',
            ],
        ];
    }
}
