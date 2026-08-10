<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'sale_price' => $this->sale_price,
            'sold_at' => $this->sold_at,
            'notes' => $this->notes,

            'unit' => new UnitResource($this->whenLoaded('unit')),

            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'email' => $this->client->email,
                    'phone' => $this->client->phone,
                    'address' => $this->client->address,
                ];
            }),

            'agent' => new UserResource($this->whenLoaded('agent')),
        ];
    }
}