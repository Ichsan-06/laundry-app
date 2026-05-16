<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Member;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = rand(8000, 200000);
        $memberDiscount = (rand(0, 1) === 1) ? ($subtotal * 0.1) : 0;
        $totalAmount = $subtotal - $memberDiscount;
        $amountReceived = ceil($totalAmount / 10) * 10;
        $changeAmount = $amountReceived - $totalAmount;

        $randomLetters = chr(rand(65, 90)) . chr(rand(65, 90));
        $randomNumbers = rand(100, 999);
        $transactionNumber = 'TRX-' . $randomLetters . $randomNumbers;

        $services = ['WASH_ONLY', 'DRY_ONLY', 'WASH_DRY', 'IRONING', 'COMPLETE'];
        $statuses = ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'];
        $payments = ['CASH', 'TRANSFER', 'E_WALLET', 'QRIS'];
        $randomTimestamp = rand(strtotime('-1 month'), time());
        $createdAt = date('Y-m-d H:i:s', $randomTimestamp);

        return [
            'outlet_id' => Outlet::first()->id ?? Outlet::factory(),
            'cashier_id' => User::first()->id ?? User::factory(),
            'member_id' => Member::inRandomOrder()->first()?->id,
            'transaction_number' => $transactionNumber,
            'service_type' => $services[array_rand($services)],
            'status' => $statuses[array_rand($statuses)],
            'subtotal' => $subtotal,
            'member_discount' => $memberDiscount,
            'total_amount' => $totalAmount,
            'payment_method' => $payments[array_rand($payments)],
            'amount_received' => $amountReceived,
            'change_amount' => $changeAmount,
            'notes' => 'Catatan transaksi nomor ' . rand(1, 100), // String biasa pengganti sentence()
            'created_at' => $createdAt,
        ];
    }

}
