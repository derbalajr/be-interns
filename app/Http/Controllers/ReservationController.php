<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            // Lock the unit for update to prevent race conditions
            $unit = Unit::lockForUpdate()->find($validated['unit_id']);

            if (!$unit) {
                return response()->json(['message' => 'Unit not found'], 404);
            }

            // Check if unit is available
            if ($unit->status !== Unit::STATUS_AVAILABLE) {
                return response()->json([
                    'message' => 'Unit is not available for reservation',
                    'current_status' => $unit->status,
                ], 422);
            }

            // Flip unit to reserved status
            $unit->status = Unit::STATUS_RESERVED;
            $unit->save();

            // Create reservation with snapshot of price
            $reservation = Reservation::create([
                'unit_id' => $unit->id,
                'client_id' => $validated['client_id'],
                'agent_id' => auth()->id(),
                'status' => 'pending',
                'reserved_price' => $unit->price,
                'reserved_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json($reservation, 201);
        });
    }

    public function index(Request $request): JsonResponse
    {
        if (!auth()->user() || !auth()->user()->can('view-reservations')) {
            abort(403, 'Unauthorized action.');
        }

        $reservations = Reservation::where('agent_id', auth()->id())
            ->with(['unit', 'client', 'agent'])
            ->latest()
            ->get();

        return response()->json($reservations);
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorizeReservation($reservation);

        $reservation->load(['unit', 'client', 'agent']);

        return response()->json($reservation);
    }

    public function cancel(Reservation $reservation): JsonResponse
    {
        if (!auth()->user() || !auth()->user()->can('cancel-reservations')) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorizeReservation($reservation);

        return DB::transaction(function () use ($reservation) {
            if ($reservation->status === 'cancelled') {
                return response()->json(['message' => 'Reservation already cancelled'], 422);
            }

            // Get the unit and lock it
            $unit = Unit::lockForUpdate()->find($reservation->unit_id);

            if (!$unit) {
                return response()->json(['message' => 'Unit not found'], 404);
            }

            // Return unit to available
            $unit->status = Unit::STATUS_AVAILABLE;
            $unit->save();

            // Cancel the reservation
            $reservation->status = 'cancelled';
            $reservation->save();

            return response()->json(['message' => 'Reservation cancelled successfully']);
        });
    }

    private function authorizeReservation(Reservation $reservation): void
    {
        if ($reservation->agent_id !== auth()->id()) {
            abort(403, 'You do not own this reservation');
        }
    }
}
