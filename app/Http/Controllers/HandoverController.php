<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHandoverRequest;
use App\Http\Resources\HandoverResource;
use App\Models\Handover;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class HandoverController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        if (!auth()->user() || !auth()->user()->can('view-handovers')) {
            abort(403, 'Unauthorized action.');
        }

        $handovers = Handover::with(['unit', 'client'])
            ->latest()
            ->paginate();

        return HandoverResource::collection($handovers);
    }

    public function store(StoreHandoverRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // A handover delivers a purchased unit, so the unit must be sold.
        $unit = Unit::find($validated['unit_id']);
        if (!$unit || $unit->status !== Unit::STATUS_SOLD) {
            return response()->json([
                'message' => 'Unit must be sold to schedule handover',
                'current_status' => $unit->status ?? null,
            ], 422);
        }

        // One active handover per unit.
        $alreadyHasHandover = Handover::where('unit_id', $unit->id)
            ->whereIn('status', ['scheduled', 'completed'])
            ->exists();
        if ($alreadyHasHandover) {
            return response()->json([
                'message' => 'This unit already has a handover.',
            ], 422);
        }

        // The client comes from the unit's reservation, never the request, so a
        // handover can't be filed against a client who didn't buy the unit.
        $reservation = Reservation::where('unit_id', $unit->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->latest()
            ->first();
        if (!$reservation) {
            return response()->json([
                'message' => 'No active reservation found for this unit.',
            ], 422);
        }

        $handover = Handover::create([
            'unit_id' => $unit->id,
            'reservation_id' => $reservation->id,
            'client_id' => $reservation->client_id,
            'handover_date' => $validated['handover_date'],
            'status' => 'scheduled',
            'notes' => $validated['notes'] ?? null,
        ]);

        $handover->load(['unit', 'client']);

        return (new HandoverResource($handover))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Handover $handover): HandoverResource
    {
        if (!auth()->user() || !auth()->user()->can('view-handovers')) {
            abort(403, 'Unauthorized action.');
        }

        $handover->load(['unit', 'client']);

        return new HandoverResource($handover);
    }

    public function complete(Handover $handover): JsonResponse
    {
        if (!auth()->user() || !auth()->user()->can('complete-handovers')) {
            abort(403, 'Unauthorized action.');
        }

        if ($handover->status === 'completed') {
            return response()->json(['message' => 'Handover already completed'], 422);
        }

        DB::transaction(function () use ($handover) {
            $handover->status = 'completed';
            $handover->save();

            // Completing the handover fulfils the reservation.
            $reservation = $handover->reservation;
            if ($reservation && $reservation->status !== 'cancelled') {
                $reservation->status = 'completed';
                $reservation->save();
            }
        });

        return response()->json(['message' => 'Handover marked as completed']);
    }
}
