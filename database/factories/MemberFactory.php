<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'member_id' => 'MEM-' . $this->faker->unique()->numberBetween(100000, 999999),
            'balance' => $this->faker->randomFloat(2, 0, 500),
            'status' => $this->faker->randomElement(['ACTIVE', 'LOW_BALANCE', 'INACTIVE', 'PREMIUM']),
            'last_transaction_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'last_transaction_details' => $this->faker->randomElement(['Washer #', 'Dryer #']) . $this->faker->numberBetween(1, 20),
        ];
    }
}
