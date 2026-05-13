<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $outlet = $user->isOwner()
            ? Outlet::query()->where('tenant_id', $user->tenant_id)->orderBy('nama_outlet')->first()
            : $user->outlet;

        return view('pages.settings.index', compact('outlet'));
    }

    public function updateOutlet(Request $request)
    {
        $request->validate([
            'nama_outlet' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'kota' => 'required|string|max:100',
            'wijayapay_merchant_code' => 'nullable|string|max:100',
            'wijayapay_api_key' => 'nullable|string',
            'wijayapay_create_url' => 'nullable|url|max:255',
            'wijayapay_status_url' => 'nullable|url|max:255',
            'wijayapay_callback_url' => 'nullable|url|max:255',
        ]);

        $user = $request->user();
        $outlet = $user->isOwner()
            ? Outlet::query()->where('tenant_id', $user->tenant_id)->orderBy('nama_outlet')->first()
            : $user->outlet;

        if (!$outlet) {
            $outlet = new Outlet([
                'tenant_id' => $user->tenant_id,
            ]);
        }

        $outlet->fill($request->only([
            'nama_outlet',
            'alamat',
            'telepon',
            'kota',
            'wijayapay_merchant_code',
            'wijayapay_api_key',
            'wijayapay_create_url',
            'wijayapay_status_url',
            'wijayapay_callback_url',
        ]));
        $outlet->save();

        return redirect()->back()->with('success', 'Pengaturan outlet berhasil diperbarui');
    }
}
