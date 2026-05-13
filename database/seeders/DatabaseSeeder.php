<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Outlet;
use App\Services\SubscriptionAccessService;
use App\Services\TenantProvisioningService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        $provisioned = app(TenantProvisioningService::class)->registerOwner([
            'tenant_name' => 'Laundry Express Utama',
            'outlet_name' => 'Laundry Express Utama',
            'owner_name' => 'Owner Laundry',
            'email' => 'admin@laundry.com',
            'password' => 'password',
            'alamat' => 'Jl. Merdeka No. 123',
            'telepon' => '021-1234567',
            'kota' => 'Jakarta',
        ], assignTrial: false);

        /** @var Tenant $tenant */
        $tenant = $provisioned['tenant'];
        /** @var Outlet $outlet */
        $outlet = $provisioned['outlet'];

        $superAdmin = User::updateOrCreate([
            'email' => 'superadmin@laundry.com',
        ], [
            'outlet_id' => null,
            'tenant_id' => null,
            'nama' => 'Super Admin',
            'email' => 'superadmin@laundry.com',
            'password_hash' => Hash::make('password'),
            'role' => 'SUPER_ADMIN',
            'user_type' => 'super_admin',
            'aktif' => true,
        ]);
        $superAdmin->syncRoles([User::ROLE_SUPER_ADMIN]);
        $superAdmin->syncLegacyRoleColumn();

        $owner = $provisioned['owner'];

        $user = User::updateOrCreate([
            'email' => 'user@laundry.com',
        ], [
            'outlet_id' => $outlet->id,
            'tenant_id' => $tenant->id,
            'nama' => 'Kasir User',
            'email' => 'user@laundry.com',
            'password_hash' => Hash::make('password'),
            'role' => 'KASIR',
            'user_type' => 'staff',
            'aktif' => true,
        ]);
        $user->syncRoles([User::ROLE_KASIR]);
        $user->syncLegacyRoleColumn();

        $trialPlan = SubscriptionPlan::where('slug', 'trial')->first();
        if ($trialPlan && ! $tenant->subscriptions()->exists()) {
            app(SubscriptionAccessService::class)->createTrialSubscription($tenant, $trialPlan, 14);
        }

        $this->call([
            MemberSeeder::class,
            TransactionSeeder::class,
            MachineSeeder::class,
        ]);
    }
}
