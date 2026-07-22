<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model$model): void
    {
        // Short-circuit 1: Artisan commands, seeders, and tinker see all records
        if (App::runningInConsole()) {
            return;
        }

        // Short-circuit 2: Unauthenticated queries (Login lookup, Sanctum token resolution)
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Scope queries to the logged-in user's tenant
        if ($user && isset($user->tenant)) {$builder->where($model->getTable() . '.tenant',$user->tenant);
            $builder;
        }
    }
}