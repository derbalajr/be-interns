<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadCollection;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request): LeadCollection
    {
        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100,
        );

        $leads = Lead::query()
            ->with('agent')
            ->latest()
            ->paginate($perPage);

        return new LeadCollection($leads);
    }

    public function store(StoreLeadRequest $request): LeadResource
    {
        $lead = Lead::create($request->validated());

        $lead->load('agent');

        return new LeadResource($lead);
    }

    public function show(Lead $lead): LeadResource
    {
        $lead->load('agent');

        return new LeadResource($lead);
    }

    public function update(
        UpdateLeadRequest $request,
        Lead $lead,
    ): LeadResource {
        $lead->update($request->validated());

        $lead->load('agent');

        return new LeadResource($lead);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json([
            'message' => 'Lead deleted successfully.',
        ]);
    }
}
