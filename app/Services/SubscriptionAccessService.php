<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionAccessService
{
    public function currentSubscription(?Tenant $tenant): ?TenantSubscription
    {
        return $tenant?->subscriptions()
            ->with('plan.permissions')
            ->latest('created_at')
            ->first();
    }

    public function isExpired(?TenantSubscription $subscription): bool
    {
        if (! $subscription) {
            return true;
        }

        if ($subscription->status === 'expired') {
            return true;
        }

        $referenceDate = $subscription->is_trial
            ? $subscription->trial_ends_at
            : $subscription->ends_at;

        return $referenceDate ? Carbon::now()->greaterThan($referenceDate) : false;
    }

    public function statusLabel(?TenantSubscription $subscription): string
    {
        if (! $subscription) {
            return 'inactive';
        }

        if ($this->isExpired($subscription)) {
            return 'expired';
        }

        if ($subscription->is_trial) {
            return 'trial';
        }

        return $subscription->status;
    }

    public function userHasFeature(User $user, string $permission): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $subscription = $this->currentSubscription($user->tenant);

        if (! $subscription || ! $subscription->plan) {
            return false;
        }

        if ($subscription->plan->slug === 'enterprise' && $subscription->plan->is_custom_permission === false) {
            return true;
        }

        return $subscription->plan->permissions->contains('name', $permission);
    }

    public function canCreateOutlet(Tenant $tenant): bool
    {
        $subscription = $this->currentSubscription($tenant);
        $plan = $subscription?->plan;

        if (! $plan || $this->isExpired($subscription)) {
            return false;
        }

        if ($plan->max_outlets === null) {
            return true;
        }

        return $tenant->outlets()->count() < $plan->max_outlets;
    }

    public function canCreateStaff(Tenant $tenant): bool
    {
        $subscription = $this->currentSubscription($tenant);
        $plan = $subscription?->plan;

        if (! $plan || $this->isExpired($subscription)) {
            return false;
        }

        if ($plan->max_staff === null) {
            return true;
        }

        return $tenant->users()->where('user_type', 'staff')->count() < $plan->max_staff;
    }

    public function createTrialSubscription(Tenant $tenant, SubscriptionPlan $plan, int $days = 14): TenantSubscription
    {
        return TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'trial',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays($days),
            'is_trial' => true,
            'grace_dashboard_only' => true,
        ]);
    }
}
