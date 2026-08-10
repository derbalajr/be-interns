<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HandoverResource extends JsonResource
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
            'handover_date' => $this->handover_date,
            'status' => $this->status,
            'notes' => $this->notes,

            'unit' => new UnitResource($this->whenLoaded('unit')),
            'client' => new ClientResource($this->whenLoaded('client')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
