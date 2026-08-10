<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unit_id' => $this->unit_id,
            'client_id' => $this->client_id,
            'agent_id' => $this->agent_id,
            'status' => $this->status,
            'reserved_price' => $this->reserved_price,
            'reserved_at' => $this->reserved_at,

            'unit' => new UnitResource($this->whenLoaded('unit')),
            'client' => new ClientResource($this->whenLoaded('client')),
            'agent' => new UserResource($this->whenLoaded('agent')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
