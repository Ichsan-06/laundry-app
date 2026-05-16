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
        $memberDiscount = $this->faker->boolean(50) ? ($subtotal * 0.1) : 0;
        $totalAmount = $subtotal - $memberDiscount;
        $amountReceived = ceil($totalAmount / 10) * 10;
        $changeAmount = $amountReceived - $totalAmount;

        return [
            'outlet_id' => Outlet::first()->id ?? Outlet::factory(),
            'cashier_id' => User::first()->id ?? User::factory(),
            'member_id' => Member::inRandomOrder()->first()?->id,
            'transaction_number' => 'TRX-' . strtoupper($this->faker->unique()->bothify('??###')),
            'service_type' => $this->faker->randomElement(['WASH_ONLY', 'DRY_ONLY', 'WASH_DRY', 'IRONING', 'COMPLETE']),
            'status' => $this->faker->randomElement(['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED']),
            'subtotal' => $subtotal,
            'member_discount' => $memberDiscount,
            'total_amount' => $totalAmount,
            'payment_method' => $this->faker->randomElement(['CASH', 'TRANSFER', 'E_WALLET', 'QRIS']),
            'amount_received' => $amountReceived,
            'change_amount' => $changeAmount,
            'notes' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
