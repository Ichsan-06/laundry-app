<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Machine::factory()->count(12)->create()->each(function ($machine) {
            // Create default durations for each machine
            \App\Models\MachineDuration::create([
                'machine_id' => $machine->id,
                'duration_type' => 'WASH',
                'duration_minutes' => 30,
                'price' => 25000.00,
                'is_active' => true,
            ]);
            \App\Models\MachineDuration::create([
                'machine_id' => $machine->id,
                'duration_type' => 'DRY',
                'duration_minutes' => 45,
                'price' => 25000.00,
                'is_active' => true,
            ]);
            \App\Models\MachineDuration::create([
                'machine_id' => $machine->id,
                'duration_type' => 'COMPLETE',
                'duration_minutes' => 90,
                'price' => 50000.00,
                'is_active' => true,
            ]);
        });
    }
}
