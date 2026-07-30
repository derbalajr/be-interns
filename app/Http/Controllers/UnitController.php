<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterUnitRequest;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

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

    $query = Unit::query()
        ->with('project');

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
    if ($request->filled('status')) {
        $query->where(
            'status',
            $request->input('status')
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
                    '%' . $location . '%'
                );
            }
        );
    }

    if ($request->input('sort') === 'price_asc') {
        $query->orderBy('price', 'asc');
    } elseif ($request->input('sort') === 'price_desc') {
        $query->orderBy('price', 'desc');
    } elseif ($request->input('sort') === 'oldest') {
        $query->oldest();
    } else {
        $query->latest();
    }

$units = $query->paginate();

    return UnitResource::collection($units);
}

    /**
     * Store a newly created unit.
     */
    public function store(StoreUnitRequest $request): UnitResource
    {
        $unit = Unit::create($request->validated());

        $unit->load('project');
        $unit->refresh();

        return new UnitResource($unit);
    }

    /**
     * Display one unit.
     */
    public function show(Unit $unit): UnitResource
    {
        Gate::authorize('view-units');

        $unit->load('project');

        return new UnitResource($unit);
    }

    /**
     * Update the unit's normal information.
     */
    public function update(
        UpdateUnitRequest $request,
        Unit $unit
    ): UnitResource {
        $unit->update($request->validated());

        $unit->load('project');

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

    public function markReserved(Unit $unit): UnitResource
    {
        Gate::authorize('edit-units');

        if ($unit->status !== Unit::STATUS_AVAILABLE) {
            abort(422, 'Only an available unit can be reserved.');
        }

        $unit->status = Unit::STATUS_RESERVED;
        $unit->save();

        $unit->load('project');

        return new UnitResource($unit);
    }

    public function markSold(Unit $unit): UnitResource
    {
        Gate::authorize('edit-units');

        if ($unit->status !== Unit::STATUS_RESERVED) {
            abort(422, 'Only a reserved unit can be sold.');
        }

        $unit->status = Unit::STATUS_SOLD;
        $unit->save();

        $unit->load('project');

        return new UnitResource($unit);
    }
}