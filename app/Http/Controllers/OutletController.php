<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Services\SubscriptionAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutletController extends Controller
{
    public function __construct(
        private readonly SubscriptionAccessService $subscriptionAccessService,
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $outlets = Outlet::query()
            ->when(! $user->isSuperAdmin(), fn ($query) => $query->where('tenant_id', $user->tenant_id))
            ->orderBy('nama_outlet')
            ->paginate(10);

        return view('pages.outlets.index', [
            'outlets' => $outlets,
        ]);
    }

    public function create(): View
    {
        return view('pages.outlets.create', [
            'outlet' => new Outlet(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user->tenant_id, 403);

        if (! $user->isSuperAdmin() && ! $this->subscriptionAccessService->canCreateOutlet($user->tenant)) {
            return back()->withErrors([
                'nama_outlet' => 'Batas outlet untuk paket langganan Anda sudah tercapai.',
            ])->withInput();
        }

        $validated = $request->validate([
            'nama_outlet' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:30'],
            'kota' => ['required', 'string', 'max:100'],
            'aktif' => ['nullable', 'boolean'],
            'wijayapay_merchant_code' => ['nullable', 'string', 'max:100'],
            'wijayapay_api_key' => ['nullable', 'string'],
            'wijayapay_create_url' => ['nullable', 'url', 'max:255'],
            'wijayapay_status_url' => ['nullable', 'url', 'max:255'],
            'wijayapay_callback_url' => ['nullable', 'url', 'max:255'],
        ]);

        Outlet::create([
            ...$validated,
            'tenant_id' => $user->tenant_id,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil ditambahkan.');
    }

    public function edit(Outlet $outlet): View
    {
        $this->authorize('update', $outlet);

        return view('pages.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet): RedirectResponse
    {
        $this->authorize('update', $outlet);

        $validated = $request->validate([
            'nama_outlet' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:30'],
            'kota' => ['required', 'string', 'max:100'],
            'aktif' => ['nullable', 'boolean'],
            'wijayapay_merchant_code' => ['nullable', 'string', 'max:100'],
            'wijayapay_api_key' => ['nullable', 'string'],
            'wijayapay_create_url' => ['nullable', 'url', 'max:255'],
            'wijayapay_status_url' => ['nullable', 'url', 'max:255'],
            'wijayapay_callback_url' => ['nullable', 'url', 'max:255'],
        ]);

        $outlet->update([
            ...$validated,
            'aktif' => $request->boolean('aktif', false),
        ]);

        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil diperbarui.');
    }

    public function destroy(Outlet $outlet): RedirectResponse
    {
        $this->authorize('delete', $outlet);

        if ($outlet->users()->exists() || $outlet->members()->exists()) {
            return back()->with('error', 'Outlet tidak bisa dihapus karena masih memiliki data terkait.');
        }

        $outlet->delete();

        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil dihapus.');
    }
}
