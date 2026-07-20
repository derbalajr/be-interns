<?php
namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
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