<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index(Request $request): UserCollection
    {
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

    $user = DB::transaction(function () use ($validated) {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'active' => true,
        ]);

        $user->assignRole($validated['role']);

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

        foreach (['name', 'email', 'password', 'active'] as $field) {
            if (array_key_exists($field, $validated)) {
                $userFields[$field] = $validated[$field];
            }
        }

        if ($userFields !== []) {
            $user->update($userFields);
        }

        if (array_key_exists('role', $validated)) {
            $user->syncRoles([$validated['role']]);
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
}