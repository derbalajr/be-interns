<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHandoverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create-handovers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // The client is not accepted from the request — it is derived from the
        // unit's reservation so a handover can never be filed against the wrong
        // client. See HandoverController::store.
        return [
            'unit_id' => 'required|exists:units,id',
            'handover_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ];
    }
}
