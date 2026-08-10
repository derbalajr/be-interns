<?php

namespace App\Http\Resources;

use App\Models\UnitMedia;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UnitResource extends JsonResource
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
            'code' => $this->code,
            'type' => $this->type,
            'area' => $this->area,
            'price' => $this->price,
            'status' => $this->status,
            'project_id' => $this->project_id,

            'project' => new ProjectResource(
                $this->whenLoaded('project')
            ),
            'photos' => $this->whenLoaded(
                'media',
                fn () => $this->media
                    ->where('type', UnitMedia::TYPE_PHOTO)
                    ->values()
                    ->map(fn ($media) => [
                        'id' => $media->id,
                        'url' => Storage::url($media->path),
                    ])
            ),

            'floor_plans' => $this->whenLoaded(
                'media',
                fn () => $this->media// gets all media records belonging to the unit.
                    ->where('type', UnitMedia::TYPE_FLOOR_PLAN)
                    ->values()
                    ->map(fn ($media) => [
                        'id' => $media->id,
                        'url' => Storage::url($media->path),
                    ])
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
