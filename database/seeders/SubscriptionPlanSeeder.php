<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            'trial' => [
                'name' => 'Trial',
                'price_monthly' => 0,
                'max_outlets' => 1,
                'max_staff' => 3,
                'is_custom_permission' => false,
                'permissions' => [
                    'dashboard.view',
                    'transactions.view',
                    'calendar.view',
                    'transactions.create',
                    'transactions.update',
                    'customers.view',
                    'customers.create',
                    'customers.update',
                    'machines.view',
                    'services.view',
                    'addons.view',
                    'cashier.access',
                    'outlets.view',
                    'outlets.update',
                    'staff.view',
                    'roles.view',
                    'settings.manage',
                    'billing.view',
                ],
            ],
            'basic' => [
                'name' => 'Basic Plan',
                'price_monthly' => 30000,
                'max_outlets' => 1,
                'max_staff' => 3,
                'is_custom_permission' => false,
                'permissions' => [
                    'dashboard.view',
                    'cashier.access',
                    'transactions.view',
                    'calendar.view',
                    'transactions.create',
                    'transactions.update',
                    'customers.view',
                    'customers.create',
                    'customers.update',
                    'staff.view',
                    'staff.create',
                    'staff.update',
                    'outlets.view',
                    'outlets.update',
                    'roles.view',
                    'roles.create',
                    'roles.update',
                    'settings.manage',
                    'billing.view',
                ],
            ],
            'pro' => [
                'name' => 'Pro Plan',
                'price_monthly' => 55000,
                'max_outlets' => 3,
                'max_staff' => 10,
                'is_custom_permission' => false,
                'permissions' => [
                    'dashboard.view',
                    'cashier.access',
                    'transactions.view',
                    'calendar.view',
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
                    'services.view',
                    'services.create',
                    'services.update',
                    'addons.view',
                    'addons.create',
                    'addons.update',
                    'reports.view',
                    'reports.export',
                    'outlets.view',
                    'outlets.create',
                    'outlets.update',
                    'staff.view',
                    'staff.create',
                    'staff.update',
                    'staff.delete',
                    'roles.view',
                    'roles.create',
                    'roles.update',
                    'settings.manage',
                    'promo.manage',
                    'billing.view',
                ],
            ],
            'enterprise' => [
                'name' => 'Enterprise Plan',
                'price_monthly' => 80000,
                'max_outlets' => null,
                'max_staff' => null,
                'is_custom_permission' => true,
                'permissions' => Permission::query()->pluck('name')->all(),
            ],
        ];

        foreach ($plans as $slug => $planData) {
            $plan = SubscriptionPlan::updateOrCreate([
                'slug' => $slug,
            ], [
                'name' => $planData['name'],
                'max_outlets' => $planData['max_outlets'],
                'max_staff' => $planData['max_staff'],
                'is_custom_permission' => $planData['is_custom_permission'],
                'is_active' => true,
                'description' => $planData['name'],
                'price_monthly' => $planData['price_monthly'] ?? 0,
            ]);

            $permissionIds = Permission::query()
                ->whereIn('name', $planData['permissions'])
                ->pluck('id')
                ->all();

            $plan->permissions()->sync($permissionIds);
        }
    }
}
