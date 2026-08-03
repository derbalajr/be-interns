<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-permissions',
            'create-projects',
            'update-projects',
            'delete-projects',
            'view-projects',

            'view-units',
            'create-units',
            'edit-units',
            'delete-units',

            'view-reservations',
            'create-reservations',
            'cancel-reservations',

            'view-clients',
            'create-clients',
            'edit-clients',
            'delete-clients',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        $agentRole = Role::firstOrCreate([
            'name' => 'agent',
            'guard_name' => 'api',
        ]);

        $managerRole = Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'api',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'api',
        ]);

        $agentRole->syncPermissions(['view-users', 'view-projects',  'view-units', 'view-reservations', 'create-reservations', 'view-clients', 'create-clients']); // Agents can view users and projects
    
        $managerRole->syncPermissions([
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'view-permissions',
            'edit-roles',
            'delete-roles',
            'view-reservations',
            'create-reservations',
            'cancel-reservations',
            'view-units',
            'create-units',
            'edit-units',
            'delete-units',
            'view-clients',
            'create-clients',
            'edit-clients',
            'delete-clients',
        ]);

        $adminRole->syncPermissions([
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-permissions',
        ]);
        $superAdminRole->syncPermissions(Permission::all());
    }
}
