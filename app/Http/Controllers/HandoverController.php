<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHandoverRequest;
use App\Http\Resources\HandoverResource;
use App\Models\Handover;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

        // Check if unit is reserved or sold
        $unit = Unit::find($validated['unit_id']);
        if (!$unit || !in_array($unit->status, [Unit::STATUS_RESERVED, Unit::STATUS_SOLD])) {
            return response()->json([
                'message' => 'Unit must be reserved or sold to schedule handover',
                'current_status' => $unit->status ?? null,
            ], 422);
        }

        $handover = Handover::create([
            'unit_id' => $validated['unit_id'],
            'client_id' => $validated['client_id'],
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

        $handover->status = 'completed';
        $handover->save();

        return response()->json(['message' => 'Handover marked as completed']);
    }
}
