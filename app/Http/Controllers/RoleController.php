<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest; // Use standard Laravel Request here
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * View all roles.
     */
    public function index(Request $request)
    {
        Gate::authorize('view-roles');

        $roles = Role::with('permissions')->get();

        return response()->json(['success' => true, 'data' => $roles], 200);
    }

    /**
     * Create a role.
     */
    public function store(StoreRoleRequest $request)
    {
        // Authorization is already handled inside StoreRoleRequest
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'api',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json(['success' => true, 'data' => $role->load('permissions')], 201);
    }

    /**
     * Update a role.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // Authorization is already handled inside UpdateRoleRequest
        $validated = $request->validated();

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json(['success' => true, 'data' => $role->load('permissions')], 200);
    }

    /**
     * Delete a role.
     */
    public function destroy(Request $request, Role $role)
    {
        // 🔒 Controller Level Authorization Check
        Gate::authorize('delete-roles');

        if (in_array($role->name, ['admin', 'super-admin'])) {
            return response()->json(['success' => false, 'message' => 'Core roles cannot be deleted.'], 403);
        }

        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully.'], 200);
    }
}
