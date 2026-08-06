<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Support\Facades\Gate;

class SaleController extends Controller
{
    public function index()
    {
         Gate::authorize('view-sales');

    $sales = Sale::query()
        ->with([
            'unit',
            'client',
            'agent',
        ])
        ->latest()
        ->paginate();

    return SaleResource::collection($sales);
    }
    public function show(Sale $sale)
    {
        Gate::authorize('view-sales');

        $sale->load([
            'unit',
            'client',
            'agent',
        ]);

        return new SaleResource($sale);
    }

}
