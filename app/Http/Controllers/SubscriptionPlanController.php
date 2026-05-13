<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::query()
            ->with('permissions')
            ->withCount('permissions')
            ->orderBy('name')
            ->paginate(10);

        return view('pages.subscription-plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('pages.subscription-plans.create', [
            'plan' => new SubscriptionPlan(['is_active' => true]),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:subscription_plans,slug'],
            'description' => ['nullable', 'string'],
            'max_outlets' => ['nullable', 'integer', 'min:1'],
            'max_staff' => ['nullable', 'integer', 'min:1'],
            'is_custom_permission' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $plan = SubscriptionPlan::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'max_outlets' => $validated['max_outlets'] ?? null,
            'max_staff' => $validated['max_staff'] ?? null,
            'is_custom_permission' => $request->boolean('is_custom_permission'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $plan->permissions()->sync(
            Permission::query()->whereIn('name', $validated['permissions'] ?? [])->pluck('id')->all()
        );

        return redirect()->route('subscription-plans.index')->with('success', 'Plan berhasil ditambahkan.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan): View
    {
        return view('pages.subscription-plans.edit', [
            'plan' => $subscriptionPlan->load('permissions'),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'selectedPermissions' => $subscriptionPlan->permissions->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:subscription_plans,slug,' . $subscriptionPlan->id],
            'description' => ['nullable', 'string'],
            'max_outlets' => ['nullable', 'integer', 'min:1'],
            'max_staff' => ['nullable', 'integer', 'min:1'],
            'is_custom_permission' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $subscriptionPlan->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'max_outlets' => $validated['max_outlets'] ?? null,
            'max_staff' => $validated['max_staff'] ?? null,
            'is_custom_permission' => $request->boolean('is_custom_permission'),
            'is_active' => $request->boolean('is_active', false),
        ]);

        $subscriptionPlan->permissions()->sync(
            Permission::query()->whereIn('name', $validated['permissions'] ?? [])->pluck('id')->all()
        );

        return redirect()->route('subscription-plans.index')->with('success', 'Plan berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        if ($subscriptionPlan->subscriptions()->exists()) {
            return back()->with('error', 'Plan tidak bisa dihapus karena sudah dipakai tenant.');
        }

        $subscriptionPlan->delete();

        return redirect()->route('subscription-plans.index')->with('success', 'Plan berhasil dihapus.');
    }
}
