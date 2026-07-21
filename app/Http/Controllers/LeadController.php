<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignLeadRequest;
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
        $this->authorize('viewAny', Lead::class);

        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100,
        );

        $query = Lead::query()->with('agent');

        if ($request->user()->isAgent()) {
            $query->where('agent_id', $request->user()->id);
        }

        $leads = $query
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
        $this->authorize('view', $lead);

        $lead->load('agent');

        return new LeadResource($lead);
    }

    public function update(
        UpdateLeadRequest $request,
        Lead $lead,
    ): LeadResource {
        $this->authorize('update', $lead);

        $lead->update($request->validated());

        $lead->load('agent');

        return new LeadResource($lead);
    }

    public function assign(
        AssignLeadRequest $request,
        Lead $lead,
    ): LeadResource {
        $this->authorize('update', $lead);

        $lead->update([
            'agent_id' => $request->validated('agent_id'),
        ]);

        $lead->load('agent');

        return new LeadResource($lead);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return response()->json([
            'message' => 'Lead deleted successfully.',
        ]);
    }
}
