<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'cashier.access',
            'transactions.view',
            'transactions.create',
            'transactions.update',
            'transactions.delete',
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'machines.view',
            'machines.create',
            'machines.update',
            'machines.delete',
            'services.view',
            'services.create',
            'services.update',
            'services.delete',
            'addons.view',
            'addons.create',
            'addons.update',
            'addons.delete',
            'reports.view',
            'reports.export',
            'settings.manage',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
            'users.manage',
            'staff.view',
            'staff.create',
            'staff.update',
            'staff.delete',
            'outlets.view',
            'outlets.create',
            'outlets.update',
            'outlets.delete',
            'billing.view',
            'billing.manage',
            'plans.manage',
            'tenants.manage',
            'promo.manage',
            'subscription.manage',
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
        $owner = Role::firstOrCreate([
            'name' => 'Owner',
            'guard_name' => 'web',
        ]);
        $kasir = Role::firstOrCreate([
            'name' => 'Kasir',
            'guard_name' => 'web',
        ]);
        $manager = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);
        $operator = Role::firstOrCreate([
            'name' => 'Operator',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions($permissions);
        $owner->syncPermissions([
            'dashboard.view',
            'cashier.access',
            'transactions.view',
            'transactions.create',
            'transactions.update',
            'transactions.delete',
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'machines.view',
            'machines.create',
            'machines.update',
            'machines.delete',
            'services.view',
            'services.create',
            'services.update',
            'services.delete',
            'addons.view',
            'addons.create',
            'addons.update',
            'addons.delete',
            'reports.view',
            'reports.export',
            'outlets.view',
            'outlets.create',
            'outlets.update',
            'outlets.delete',
            'staff.view',
            'staff.create',
            'staff.update',
            'staff.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'settings.manage',
            'billing.view',
        ]);
        $kasir->syncPermissions([
            'dashboard.view',
            'cashier.access',
            'transactions.view',
            'transactions.create',
            'customers.view',
            'customers.create',
        ]);
        $manager->syncPermissions([
            'dashboard.view',
            'cashier.access',
            'transactions.view',
            'transactions.create',
            'transactions.update',
            'customers.view',
            'customers.create',
            'customers.update',
            'reports.view',
        ]);
        $operator->syncPermissions([
            'dashboard.view',
            'machines.view',
            'services.view',
            'addons.view',
        ]);
    }
}
