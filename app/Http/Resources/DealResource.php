<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'lead_id' => $this->lead_id,
            'unit_id' => $this->unit_id,
            'agent_id' => $this->agent_id,
            'stage' => $this->stage,
            'value' => $this->value,
            'expected_close' => $this->expected_close,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
