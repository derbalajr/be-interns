<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (App::runningInConsole()) {
            return;
        }

        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        if ($user && isset($user->tenant)) {
            $builder->where($model->getTable().'.tenant', $user->tenant);
        }
    }
}
