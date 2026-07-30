<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): UserCollection
    {
        Gate::authorize('view-users');

        $perPage = min(
            max($request->integer('per_page', 15), 1),
            100,
        );

        $users = User::query()
            ->with('roles')
            ->latest()
            ->paginate($perPage);

        return new UserCollection($users);
    }

   public function store(StoreUserRequest $request)
{
    $validated = $request->validated();

    // New users belong to the same tenant/workspace as their creator.
    $tenant = $request->user()?->tenant;

    $user = DB::transaction(function () use ($validated, $tenant) {
        $role = Role::query()
            ->where('guard_name', 'api')
            ->findOrFail($validated['role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'active' => $validated['active'] ?? true,
            'tenant' => $tenant,
        ]);

        $user->assignRole($role);

        return $user;
    });

    $user->load('roles');

    return (new UserResource($user))
        ->response()
        ->setStatusCode(201);
}

   public function update(
    UpdateUserRequest $request,
    User $user
): UserResource {
    $validated = $request->validated();

    DB::transaction(function () use ($validated, $user) {
        $userFields = [];

        foreach (
            ['name', 'email', 'password', 'active'] as $field
        ) {
            if (array_key_exists($field, $validated)) {
                $userFields[$field] = $validated[$field];
            }
        }

        if ($userFields !== []) {
            $user->update($userFields);
        }

        if (array_key_exists('role_id', $validated)) {
            $role = Role::query()
                ->where('guard_name', 'api')
                ->findOrFail($validated['role_id']);

            $user->syncRoles([$role]);
        }

        if (
            array_key_exists('active', $validated)
            && $validated['active'] === false
        ) {
            $user->tokens()->delete();
        }
    });

    return new UserResource(
        $user->fresh()->load('roles')
    );
}

    public function destroy(User $user): Response
    {
        Gate::authorize('delete-users');

        $user->delete();

        return response()->noContent();
    }
}
