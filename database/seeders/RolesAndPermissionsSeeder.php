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

        $agentRole->syncPermissions([]);

        $managerRole->syncPermissions([
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'view-permissions',
            'edit-roles',
            'delete-roles',
        ]);

        $adminRole->syncPermissions([
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
        ]);

        // 2. Define Admin Role & Assign Permissions
        // firstOrCreate prevents duplicate role errors
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $agentRole->syncPermissions(['view-users', 'view-projects']); // Agents can view users and projects
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions(['view-users', 'create-users', 'edit-users', 'view-roles', 'view-permissions', 'edit-roles', 'delete-roles', 'create-projects', 'update-projects', 'delete-projects', 'view-projects']); // Managers have broader permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole->syncPermissions(['view-users', 'create-users', 'edit-users', 'delete-users', 'view-roles', 'view-permissions', 'view-projects']); // Admins have full permissions
        // syncPermissions ensures permissions are assigned cleanly without duplicating pivot table rows
        $superAdminRole->syncPermissions(Permission::all());
    }
}
