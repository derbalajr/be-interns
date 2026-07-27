<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DealController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Deal::class);

        $query = Deal::query()
            ->with('lead', 'agent', 'unit');

        if ($request->user()->isAgent()) {
            $query->where('agent_id', $request->user()->id);
        }

        $deals = $query->paginate(15);

        return DealResource::collection($deals);
    }

    public function store(StoreDealRequest $request): DealResource
    {
        $this->authorize('create', Deal::class);

        $deal = Deal::create($request->validated());

        $deal->load('lead', 'agent', 'unit');

        return new DealResource($deal);
    }

    public function show(Deal $deal): DealResource
    {
        $this->authorize('view', $deal);

        $deal->load('lead', 'agent', 'unit');

        return new DealResource($deal);
    }

    public function update(
        UpdateDealRequest $request,
        Deal $deal,
    ): DealResource {
        $this->authorize('update', $deal);

        $deal->update($request->validated());

        $deal->load('lead', 'agent', 'unit');

        return new DealResource($deal);
    }

    public function destroy(Deal $deal): JsonResponse
    {
        $this->authorize('delete', $deal);

        $deal->delete();

        return response()->json([
            'message' => 'Deal deleted successfully.',
        ]);
    }
}
