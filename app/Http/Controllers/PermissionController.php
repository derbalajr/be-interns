<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * View all available system permissions.
     */
    public function index()
    {
        Gate::authorize('view-permissions');

        $permissions = Permission::all();

        return response()->json(['success' => true, 'data' => $permissions], 200);

    }
}
