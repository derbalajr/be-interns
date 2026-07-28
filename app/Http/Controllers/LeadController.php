<?php

namespace App\Http\Controllers;

use App\Enums\LeadStage;
use App\Http\Requests\AssignLeadRequest;
use App\Http\Requests\ChangeLeadStageRequest;
use App\Http\Requests\IndexLeadRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadCollection;
use App\Http\Resources\LeadResource;
use App\Http\Resources\UnitResource;
use App\Models\Lead;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class LeadController extends Controller
{
    public function index(IndexLeadRequest $request): LeadCollection
    {
        $this->authorize('viewAny', Lead::class);

        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100,
        );

        $query = Lead::query()
            ->with('agent')
            ->stage($request->query('stage'))
            ->source($request->query('source'))
            ->budgetRange(
                $request->query('min_budget'),
                $request->query('max_budget'),
            )
            ->search($request->query('q'));

        if ($request->user()->isAgent()) {
            $query->where('agent_id', $request->user()->id);
        }

        $sort = $request->query('sort', '-created_at');

        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        $leads = $query
            ->orderBy($column, $direction)
            ->paginate($perPage);

        return new LeadCollection($leads);
    }

    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create($request->validated());

        $lead->refresh();

        return (new LeadResource($lead))
            ->additional([
                'meta' => [
                    'success' => true,
                ],
            ])
            ->response()
            ->setStatusCode(201);
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

    public function changeStage(
        ChangeLeadStageRequest $request,
        Lead $lead,
    ): LeadResource {
        $this->authorize('update', $lead);

        $targetStage = LeadStage::from(
            $request->validated('stage'),
        );

        if (! $lead->canTransitionTo($targetStage)) {
            throw ValidationException::withMessages([
                'stage' => [
                    "A lead cannot move from {$lead->stage->value} to {$targetStage->value}.",
                ],
            ]);
        }

        $lead->transitionTo($targetStage);
        $lead->load('agent');

        return new LeadResource($lead);
    }

    public function shortlist(Lead $lead): AnonymousResourceCollection
    {
        $this->authorize('view', $lead);

        $units = $lead->units()
            ->with('project')
            ->latest('lead_unit.created_at')
            ->get();

        return UnitResource::collection($units);
    }

    public function addToShortlist(
        Lead $lead,
        Unit $unit,
    ): UnitResource {
        $this->authorize('update', $lead);

        $lead->units()->syncWithoutDetaching([
            $unit->id,
        ]);

        $unit->load('project');

        return new UnitResource($unit);
    }

    public function removeFromShortlist(
        Lead $lead,
        Unit $unit,
    ): JsonResponse {
        $this->authorize('update', $lead);

        $lead->units()->detach($unit->id);

        return response()->json([
            'message' => 'Unit removed from lead shortlist successfully.',
        ]);
    }
}
