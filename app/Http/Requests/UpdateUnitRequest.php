<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('edit-units');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $unit = $this->route('unit');

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:100',

                Rule::unique('units', 'code')
                    ->where(
                        fn ($query) => $query->where(
                            'project_id',
                            $this->input('project_id', $unit->project_id)
                        )
                    )
                    ->ignore($unit->id),
            ],

            'type' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'area' => [
                'sometimes',
                'required',
                'numeric',
                'gt:0',
            ],

            'price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'project_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:projects,id',
            ],

            'status' => [
                'prohibited',
            ],
        ];
    }
}
