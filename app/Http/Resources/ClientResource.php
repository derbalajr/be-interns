<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Explicitly whitelists fields so soft-delete's `deleted_at` and any
     * future internal columns are never leaked. The KYC/PII fields
     * (national_id, full_arabic_name, birthdate, ...) are part of the client
     * record and only reach here behind the `view-clients` permission.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'national_id' => $this->national_id,
            'full_arabic_name' => $this->full_arabic_name,
            'marital_status' => $this->marital_status,
            'job' => $this->job,
            'expiry_date' => $this->expiry_date,
            'birthdate' => $this->birthdate,

            'reservations' => ReservationResource::collection(
                $this->whenLoaded('reservations')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
