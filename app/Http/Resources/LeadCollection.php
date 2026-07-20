<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LeadCollection extends BaseResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => LeadResource::collection($this->collection),
        ];
    }
}
