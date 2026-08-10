<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('edit-clients');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->route('client')->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:clients,email,' . $clientId,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string|unique:clients,national_id,' . $clientId,
            'full_arabic_name' => 'nullable|string',
            'marital_status' => 'nullable|in:single,married',
            'job' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'birthdate' => 'nullable|date',
        ];
    }
}
