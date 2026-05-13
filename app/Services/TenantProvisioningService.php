<?php

namespace App\Services;

use App\Models\Outlet;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantProvisioningService
{
    public function registerOwner(array $data, bool $assignTrial = true): array
    {
        return DB::transaction(function () use ($data, $assignTrial) {
            $tenant = Tenant::create([
                'name' => $data['tenant_name'],
                'slug' => $this->uniqueTenantSlug($data['tenant_name']),
                'status' => 'active',
            ]);

            $outlet = Outlet::create([
                'tenant_id' => $tenant->id,
                'nama_outlet' => $data['outlet_name'],
                'alamat' => $data['alamat'] ?? '-',
                'telepon' => $data['telepon'] ?? '-',
                'kota' => $data['kota'] ?? '-',
                'aktif' => true,
            ]);

            $owner = User::create([
                'tenant_id' => $tenant->id,
                'outlet_id' => null,
                'nama' => $data['owner_name'],
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'role' => User::legacyRoleFromRoleName(User::ROLE_OWNER),
                'user_type' => 'owner',
                'aktif' => true,
            ]);
            $owner->syncRoles([User::ROLE_OWNER]);
            $owner->syncLegacyRoleColumn();

            $tenant->update([
                'owner_user_id' => $owner->id,
            ]);

            if ($assignTrial) {
                $trialPlan = SubscriptionPlan::query()->where('slug', 'trial')->first();

                if ($trialPlan) {
                    app(SubscriptionAccessService::class)->createTrialSubscription($tenant, $trialPlan, 14);
                }
            }

            return compact('tenant', 'outlet', 'owner');
        });
    }

    private function uniqueTenantSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $iteration = 1;

        while (Tenant::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $iteration;
            $iteration++;
        }

        return $slug;
    }
}
