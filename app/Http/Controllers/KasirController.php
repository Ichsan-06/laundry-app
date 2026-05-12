<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\Machine;
use App\Models\Transaction;
use App\Models\MachineDuration;
use App\Models\SelfServiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $members = \App\Models\Member::orderBy('nama', 'asc')->get();
        $machines = \App\Models\Machine::with('durations')->get();
        $services = \App\Models\ServicePackage::where('aktif', true)->get();
        $addons = \App\Models\AddonOption::where('aktif', true)->get();
        
        return view('pages.kasir', compact('members', 'machines', 'services', 'addons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => 'required|in:SELF_SERVICE,DROP_OFF',
            'member_id' => 'nullable|exists:members,id',
            'payment_method' => 'required|string',
            'amount_received' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            
            // Self Service Fields
            'machine_ids' => 'required_if:service_type,SELF_SERVICE|string|nullable',
            
            // Drop Off Fields
            'drop_off_details' => 'required_if:service_type,DROP_OFF|string|nullable',
            'addon_ids' => 'nullable|string',
            'items' => 'required_if:service_type,DROP_OFF|string|nullable',
            'note' => 'nullable|string',
            'estimated_finish' => 'nullable|string',
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
                $member = $validated['member_id'] ? \App\Models\Member::find($validated['member_id']) : null;
                $cashier = \App\Models\User::first(); // Assuming first user for now
                $outletId = \App\Models\Outlet::first()->id;

                $subtotal = 0;
                $totalWeight = 0;
                $transactionDetails = [];
                $dropOffDetails = [];

                if ($validated['service_type'] === 'SELF_SERVICE') {
                    $machineIds = explode(',', $validated['machine_ids']);
                    $machines = \App\Models\Machine::whereIn('id', $machineIds)->get();
                    
                    foreach ($machines as $machine) {
                        $duration = $machine->durations()
                            ->where('duration_type', $machine->machine_type === 'WASHER' ? 'WASH' : 'DRY')
                            ->first() ?? $machine->durations()->first();
                        
                        if ($duration) {
                            $subtotal += $duration->price;
                            $transactionDetails[] = [
                                'type' => 'MACHINE',
                                'machine' => $machine,
                                'duration' => $duration
                            ];
                        }
                    }
                } else {
                    // Drop Off
                    $dropOffDetails = json_decode($validated['drop_off_details'], true);
                    foreach ($dropOffDetails as $detail) {
                        if (!empty($detail['package_id'])) {
                            $pkg = \App\Models\ServicePackage::find($detail['package_id']);
                            if ($pkg) {
                                $weight = floatval($detail['weight'] ?? 1);
                                $totalWeight += $weight;
                                $actualWeight = max($weight, floatval($pkg->berat_minimal));
                                $subtotal += $actualWeight * floatval($pkg->harga_per_kg);
                            }
                        }
                    }

                    $addonIds = array_filter(explode(',', $validated['addon_ids'] ?? ''));
                    if (!empty($addonIds)) {
                        $addons = \App\Models\AddonOption::whereIn('id', $addonIds)->get();
                        foreach ($addons as $addon) {
                            $subtotal += floatval($addon->harga);
                        }
                    }
                }

                // Create Transaction
                $transaction = \App\Models\Transaction::create([
                    'outlet_id' => $outletId,
                    'cashier_id' => $cashier->id,
                    'member_id' => $member?->id,
                    'transaction_number' => 'TRX-' . strtoupper(bin2hex(random_bytes(3))),
                    'transaction_type' => $validated['service_type'],
                    'service_type' => $validated['service_type'] === 'SELF_SERVICE' ? 'COMPLETE' : 'COMPLETE',
                    'weight' => $validated['service_type'] === 'DROP_OFF' ? $totalWeight : null,
                    'estimated_finish' => $validated['service_type'] === 'DROP_OFF' && $validated['estimated_finish'] ? $validated['estimated_finish'] : null,
                    'status' => 'IN_PROGRESS',
                    'subtotal' => $subtotal,
                    'member_discount' => 0,
                    'discount_percent' => $validated['discount_percent'] ?? 0,
                    'discount_amount' => $validated['discount_amount'] ?? 0,
                    'tax_percent' => $validated['tax_percent'] ?? 0,
                    'tax_amount' => $validated['tax_amount'] ?? 0,
                    'total_amount' => $validated['total_amount'],
                    'payment_method' => $validated['payment_method'],
                    'amount_received' => $validated['amount_received'],
                    'change_amount' => $validated['amount_received'] - $validated['total_amount'],
                    'notes' => $validated['note'] ?? null,
                ]);

                // Save Specific Details
                if ($validated['service_type'] === 'SELF_SERVICE') {
                    foreach ($transactionDetails as $detail) {
                        \App\Models\SelfServiceDetail::create([
                            'transaction_id' => $transaction->id,
                            'machine_id' => $detail['machine']->id,
                            'machine_duration_id' => $detail['duration']->id,
                            'duration_minutes' => $detail['duration']->duration_minutes,
                            'price' => $detail['duration']->price,
                            'start_time' => now(),
                            'end_time' => now()->addMinutes($detail['duration']->duration_minutes),
                            'machine_status' => 'RUNNING',
                        ]);
                        $detail['machine']->update(['status' => 'IN_USE']);
                    }
                } else {
                    // Drop Off
                    // Save Services Packages with Weight and Note
                    foreach ($dropOffDetails as $detail) {
                        if (!empty($detail['package_id'])) {
                            $pkg = \App\Models\ServicePackage::find($detail['package_id']);
                            if ($pkg) {
                                $transaction->servicePackages()->attach($pkg->id, [
                                    'id' => \Illuminate\Support\Str::uuid(),
                                    'weight' => $detail['weight'] ?? 1,
                                    'note' => $detail['note'] ?? null,
                                    'price' => $pkg->harga_per_kg
                                ]);
                            }
                        }
                    }

                    // Save Addons Pivot
                    $addonIds = array_filter(explode(',', $validated['addon_ids'] ?? ''));
                    if (!empty($addonIds)) {
                        $addons = \App\Models\AddonOption::whereIn('id', $addonIds)->get();
                        foreach ($addons as $addon) {
                            $transaction->addonOptions()->attach($addon->id, [
                                'id' => \Illuminate\Support\Str::uuid(),
                                'price' => $addon->harga
                            ]);
                        }
                    }

                    // Save Items
                    $items = json_decode($validated['items'], true);
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            if (!empty($item['nama'])) {
                                \App\Models\TransactionItem::create([
                                    'transaction_id' => $transaction->id,
                                    'nama_item' => $item['nama'],
                                    'qty' => $item['qty'] ?? 1,
                                    'note' => $item['note'] ?? null,
                                ]);
                            }
                        }
                    }
                }

                return redirect()->route('kasir.index')->with([
                    'success' => 'Transaksi berhasil diproses.',
                    'last_transaction' => [
                        'id' => $transaction->id,
                        'number' => $transaction->transaction_number,
                        'total' => $transaction->total_amount,
                        'received' => $transaction->amount_received,
                        'change' => $transaction->change_amount,
                    ]
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function printReceipt($id)
    {
        $transaction = Transaction::with([
            'member', 
            'cashier', 
            'selfServiceDetails.machine',
            'servicePackages',
            'addonOptions',
            'items'
        ])->findOrFail($id);
        
        return view('pages.receipt', compact('transaction'));
    }

    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['outlet_id'] = \App\Models\Outlet::first()->id;
        $validated['id_member'] = 'MBR-' . strtoupper(bin2hex(random_bytes(2)));
        $validated['saldo'] = 0;
        $validated['status'] = 'AKTIF';
        $validated['tanggal_daftar'] = now();

        $member = Member::create($validated);

        return response()->json([
            'success' => true,
            'member' => $member
        ]);
    }
}
