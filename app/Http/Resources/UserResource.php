<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $role = $this->roles->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'tenant' => $this->tenant,

            // Array of role NAMES — used by the assign-agent select, which
            // filters on user.roles (e.g. role === 'agent').
            'roles' => $this->getRoleNames(),

            // Single role object — used by the user-management screen.
            // Null-safe so a user with no role doesn't 500 the whole list.
            'role' => $role ? [
                'id' => $role->id,
                'name' => $role->name,
            ] : null,

            'permissions' => $this->getAllPermissions()->pluck('name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
