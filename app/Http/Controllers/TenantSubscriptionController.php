<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantSubscriptionController extends Controller
{
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'status' => ['required', 'in:trial,active,expired,inactive'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'trial_ends_at' => ['nullable', 'date'],
            'is_trial' => ['nullable', 'boolean'],
            'grace_dashboard_only' => ['nullable', 'boolean'],
        ]);

        $plan = SubscriptionPlan::query()->findOrFail($validated['subscription_plan_id']);

        TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => $validated['status'],
            'starts_at' => $validated['starts_at'] ?? now(),
            'ends_at' => $validated['ends_at'] ?? null,
            'trial_ends_at' => $validated['trial_ends_at'] ?? null,
            'is_trial' => $request->boolean('is_trial'),
            'grace_dashboard_only' => $request->boolean('grace_dashboard_only', true),
            'expired_at' => $validated['status'] === 'expired' ? now() : null,
        ]);

        return redirect()->route('tenants.edit', $tenant)->with('success', 'Subscription tenant berhasil diperbarui.');
    }
}
