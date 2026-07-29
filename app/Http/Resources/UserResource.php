<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $role = $this->roles()->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'tenant' => $this->tenant,

            'role' => [
                'id' => $role->id,
                'name' => $role->name,
            ],

            'permissions' => $this->getAllPermissions()->pluck('name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}