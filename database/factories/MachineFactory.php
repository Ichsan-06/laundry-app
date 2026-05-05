<?php

namespace Database\Factories;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'outlet_id' => \App\Models\Outlet::first()->id ?? \App\Models\Outlet::factory(),
            'machine_code' => $this->faker->randomElement(['WS', 'DR']) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'machine_type' => $this->faker->randomElement(['WASHER', 'DRYER']),
            'capacity_kg' => $this->faker->randomElement([7.0, 10.0, 12.0, 15.0]),
            'status' => $this->faker->randomElement(['AVAILABLE', 'IN_USE', 'MAINTENANCE', 'FAULTY']),
            'brand' => $this->faker->randomElement(['LG', 'Samsung', 'Electrolux', 'Whirlpool']),
            'last_serviced_at' => $this->faker->dateTimeBetween('-6 months', '-1 week'),
        ];
    }
}
