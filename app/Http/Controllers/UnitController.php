<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
  use Illuminate\Http\Request;
class UnitController extends Controller
{
    /**
     * Display a paginated list of units.
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('view-units');

        $units = Unit::query()
            ->with('project')
            ->latest()
            ->paginate();

        return UnitResource::collection($units);
    }

    /**
     * Store a newly created unit.
     */
    public function store(StoreUnitRequest $request): UnitResource
    {
        $unit = Unit::create($request->validated());
        $unit->load('project');
        $unit->refresh(); // Refresh the model to get the latest data from the database

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

  

 public function markSold(Request $request, Unit $unit): UnitResource
  {
    Gate::authorize('edit-units');

    if ($unit->status !== Unit::STATUS_RESERVED) {
        abort(422, 'Only a reserved unit can be sold.');
    }

    $reservation = Reservation::where('unit_id', $unit->id)
        ->latest()
        ->first();

    if (! $reservation) {
        abort(422, 'No reservation found for this unit.');
    }

    DB::transaction(function () use ($unit, $reservation, $request) {
        $unit->update([
            'status' => Unit::STATUS_SOLD,
        ]);

        Sale::create([
            'unit_id'    => $unit->id,
            'client_id'  => $reservation->client_id,
            'agent_id' => $request->user()->id,
            'sale_price' => $unit->price,
            'sold_at'    => now(),
            'notes'      => null,
        ]);
    });

    $unit->load('project');

    return new UnitResource($unit);
   }
}
