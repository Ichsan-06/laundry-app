<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // For now, we'll edit the first outlet
        $outlet = Outlet::first();
        return view('pages.settings.index', compact('outlet'));
    }

    public function updateOutlet(Request $request)
    {
        $request->validate([
            'nama_outlet' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'kota' => 'required|string|max:100',
        ]);

        $outlet = Outlet::first();
        if (!$outlet) {
            $outlet = new Outlet();
        }

        $outlet->fill($request->all());
        $outlet->save();

        return redirect()->back()->with('success', 'Pengaturan outlet berhasil diperbarui');
    }
}
