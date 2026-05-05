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
        $members = Member::orderBy('nama', 'asc')->get();
        $machines = Machine::with('durations')->get();
        
        return view('pages.kasir', compact('members', 'machines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'machine_ids' => 'required|string',
            'payment_method' => 'required|string',
            'amount_received' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                $machineIds = explode(',', $validated['machine_ids']);
                $machines = Machine::whereIn('id', $machineIds)->get();
                $member = $validated['member_id'] ? Member::find($validated['member_id']) : null;
                $cashier = User::first(); 

                // Calculate total price from all machines
                $totalPrice = 0;
                $details = [];

                foreach ($machines as $machine) {
                    // Get default duration (Wash for Washer, Dry for Dryer)
                    $duration = $machine->durations()
                        ->where('duration_type', $machine->machine_type === 'WASHER' ? 'WASH' : 'DRY')
                        ->first() ?? $machine->durations()->first();
                    
                    if ($duration) {
                        $totalPrice += $duration->price;
                        $details[] = [
                            'machine' => $machine,
                            'duration' => $duration
                        ];
                    }
                }

                // 1. Create Transaction
                $transaction = Transaction::create([
                    'outlet_id' => $machines->first()->outlet_id,
                    'cashier_id' => $cashier->id,
                    'member_id' => $member?->id,
                    'transaction_number' => 'TRX-' . strtoupper(bin2hex(random_bytes(3))),
                    'service_type' => count($machineIds) > 1 ? 'COMPLETE' : ($machines->first()->machine_type === 'WASHER' ? 'WASH' : 'DRY'),
                    'status' => 'IN_PROGRESS',
                    'subtotal' => $totalPrice,
                    'member_discount' => 0,
                    'total_amount' => $totalPrice,
                    'payment_method' => $validated['payment_method'],
                    'amount_received' => $validated['amount_received'],
                    'change_amount' => $validated['amount_received'] - $totalPrice,
                ]);

                // 2. Create Self Service Details & Update Machines
                foreach ($details as $detail) {
                    SelfServiceDetail::create([
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
        $transaction = Transaction::with(['member', 'cashier', 'selfServiceDetails.machine'])->findOrFail($id);
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
