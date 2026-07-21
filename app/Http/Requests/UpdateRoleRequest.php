<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('edit-roles');
    }

    public function rules(): array
    {
        // Get the role instance or ID from the route parameter
        $role = $this->route('role'); 

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('roles', 'name')->ignore($role),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }
}