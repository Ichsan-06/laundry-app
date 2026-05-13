<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Services\TenantProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::query()
            ->with(['owner', 'outlets', 'activeSubscription.plan'])
            ->orderBy('name')
            ->paginate(10);

        return view('pages.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('pages.tenants.create');
    }

    public function store(Request $request, TenantProvisioningService $tenantProvisioningService): RedirectResponse
    {
        $validated = $request->validate([
            'owner_name' => ['required', 'string', 'max:255'],
            'tenant_name' => ['required', 'string', 'max:255'],
            'outlet_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telepon' => ['required', 'string', 'max:30'],
            'kota' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $tenantProvisioningService->registerOwner($validated);

        return redirect()->route('tenants.index')->with('success', 'Tenant owner baru berhasil dibuat.');
    }

    public function edit(Tenant $tenant): View
    {
        return view('pages.tenants.edit', [
            'tenant' => $tenant->load(['owner', 'activeSubscription.plan']),
            'plans' => SubscriptionPlan::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $tenant->update($validated);

        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil diperbarui.');
    }
}
