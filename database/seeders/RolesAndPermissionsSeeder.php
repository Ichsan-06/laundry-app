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
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'access cashier',
            'manage members',
            'manage transactions',
            'manage machines',
            'manage users',
            'manage addons',
            'manage services',
            'view reports',
            'export reports',
            'manage settings',
            'manage roles',
            'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        $user = Role::firstOrCreate([
            'name' => 'User',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions([
            'view dashboard',
            'access cashier',
            'manage members',
            'manage transactions',
            'manage machines',
            'manage users',
            'manage addons',
            'manage services',
            'view reports',
            'export reports',
            'manage settings',
            'manage roles',
            'manage permissions',
        ]);
        $user->syncPermissions([
            'access cashier',
            'manage members',
            'manage transactions',
        ]);
    }
}
