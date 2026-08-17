<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterUnitRequest;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Models\UnitMedia;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
  use Illuminate\Http\Request;
class UnitController extends Controller
{
    /**
     * Display a paginated list of units.
     */
    public function index(
        FilterUnitRequest $request
    ): AnonymousResourceCollection {
        Gate::authorize('view-units');

        $validated = $request->validated();

        $query = Unit::query()->with([
            'project',
            'media',
        ]);

        // Validated inside FilterUnitRequest
        if (isset($validated['min_price'])) {
            $query->where(
                'price',
                '>=',
                $validated['min_price']
            );
        }

        // Validated inside FilterUnitRequest
        if (isset($validated['max_price'])) {
            $query->where(
                'price',
                '<=',
                $validated['max_price']
            );
        }

        // Normal if condition — no validation
        if ($request->filled('type')) {
            $query->where(
                'type',
                $request->input('type')
            );
        }

        // Normal if condition — no validation
        if (isset($validated['status'])) {
            $query->where(
                'status',
                $validated['status']
            );
        }

        // Normal if condition — no validation
        if ($request->filled('location')) {
            $location = $request->input('location');

            $query->whereHas(
                'project',
                function ($projectQuery) use ($location) {
                    $projectQuery->where(
                        'location',
                        'like',
                        '%'.$location.'%'
                    );
                }
            );
        }

        $sort = $validated['sort'] ?? 'latest';

        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $units = $query
            ->paginate()
            ->withQueryString();

        return UnitResource::collection($units);
    }

    /**
     * Store a newly created unit.
     */
    public function store(StoreUnitRequest $request): UnitResource
    {
        $validated = $request->validated();

        $unitData = collect($validated)->except([
            'photos',
            'floor_plans',
        ])->toArray();

        $unit = Unit::create($unitData);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('units', 'public');

                $unit->media()->create([
                    'path' => $path,
                    'type' => UnitMedia::TYPE_PHOTO,
                ]);
            }
        }

        if ($request->hasFile('floor_plans')) {
            foreach ($request->file('floor_plans') as $floorPlan) {
                $path = $floorPlan->store('units', 'public');

                $unit->media()->create([
                    'path' => $path,
                    'type' => UnitMedia::TYPE_FLOOR_PLAN,
                ]);
            }
        }

        $unit->load([
            'project',
            'media',
        ]);

        return new UnitResource($unit);
    }

    /**
     * Display one unit.
     */
    public function show(Unit $unit): UnitResource
    {
        Gate::authorize('view-units');

        $unit->load([
            'project',
            'media',
        ]);

        return new UnitResource($unit);
    }

    /**
     * Update the unit's normal information.
     */
   /**
 * Update the unit.
 */
public function update(
    UpdateUnitRequest $request,
    Unit $unit
): UnitResource {
    $validated = $request->validated();

    // 1. Update normal unit fields only
    $unitData = collect($validated)
        ->except('media')
        ->toArray();

    $unit->update($unitData);

    // 2. Only touch media if frontend sent "media"
    if ($request->has('media')) {
        $media = $request->input('media', []);

        // 3. Get IDs of existing media that frontend kept
        $sentMediaIds = collect($media)
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        // 4. Find media that frontend removed
        $mediaToDelete = $unit->media()
            ->whereNotIn('id', $sentMediaIds)
            ->get();

        // Delete both the physical file and database row
        foreach ($mediaToDelete as $mediaItem) {
            Storage::disk('public')
                ->delete($mediaItem->path);

            $mediaItem->delete();
        }

        // 5. Update existing media / create new media
        foreach ($media as $index => $mediaItem) {

            // Existing media
            if (!empty($mediaItem['id'])) {
                $existingMedia = $unit->media()
                    ->find($mediaItem['id']);

                $existingMedia?->update([
                    'type' => $mediaItem['type'],
                ]);

                continue;
            }

            // New media
            if ($request->hasFile("media.$index.file")) {
                $path = $request
                    ->file("media.$index.file")
                    ->store('units', 'public');

                $unit->media()->create([
                    'path' => $path,
                    'type' => $mediaItem['type'],
                ]);
            }
        }
    }

    $unit->load([
        'project',
        'media',
    ]);

    return new UnitResource($unit);
}
    /**
     * Delete the unit.
     */
    public function destroy(Unit $unit): Response
    {
        Gate::authorize('delete-units');

        $unit->delete();

        return response()->noContent();
    }


 public function markSold(Request $request, Unit $unit): UnitResource
{
    Gate::authorize('edit-units');

    if ($unit->status !== Unit::STATUS_RESERVED) {
        abort(422, 'Only a reserved unit can be sold.');
    }

    $reservation = Reservation::where('unit_id', $unit->id)
        ->whereIn('status', ['pending', 'confirmed'])
        ->latest()
        ->first();

    if (! $reservation) {
        abort(422, 'No active reservation found for this unit.');
    }

    DB::transaction(function () use ($unit, $reservation, $request) {
        $unit->update([
            'status' => Unit::STATUS_SOLD,
        ]);

        // The sale confirms the reservation (pending -> confirmed).
        $reservation->update([
            'status' => 'confirmed',
        ]);

        Sale::create([
            'unit_id' => $unit->id,
            'client_id' => $reservation->client_id,
            'agent_id' => $request->user()->id,
            'sale_price' => $reservation->reserved_price,
            'sold_at' => now(),
            'notes' => null,
        ]);
    });

    $unit->load('project');

    return new UnitResource($unit);
}
}
