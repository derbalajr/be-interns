<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LeadResource extends BaseResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'source' => $this->source,
            'stage' => $this->stage,
            'budget' => $this->budget,
            'agent' => new UserResource($this->whenLoaded('agent')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
