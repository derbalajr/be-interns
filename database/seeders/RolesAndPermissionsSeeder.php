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
        // Reset cached roles and permissions to avoid guard conflicts
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define CRM Permissions
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
        ];

        foreach ($permissions as $permission) {
            // firstOrCreate prevents duplicate permission errors if the seeder runs again
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Define Admin Role & Assign Permissions
        // firstOrCreate prevents duplicate role errors
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $agentRole->syncPermissions(['view-users', 'view-projects',  'view-units',]); // Agents can view users and projects
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions(['view-users', 'create-users', 'edit-users', 'view-roles', 'view-permissions', 'edit-roles', 'delete-roles', 'create-projects', 'update-projects', 'delete-projects', 'view-projects','view-units',
'create-units',
'edit-units',
'delete-units',]); // Managers have broader permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole->syncPermissions(['view-users', 'create-users', 'edit-users', 'delete-users', 'view-roles', 'view-permissions', 'view-projects']); // Admins have full permissions
        // syncPermissions ensures permissions are assigned cleanly without duplicating pivot table rows
        $superAdminRole->syncPermissions(Permission::all());
    }
}
