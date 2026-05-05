<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default outlet
        $outlet = Outlet::create([
            'nama_outlet' => 'Laundry Express Utama',
            'alamat' => 'Jl. Merdeka No. 123',
            'telepon' => '021-1234567',
            'kota' => 'Jakarta',
            'aktif' => true,
        ]);

        // Create a super admin user
        User::create([
            'outlet_id' => $outlet->id,
            'nama' => 'Super Admin',
            'email' => 'admin@laundry.com',
            'password_hash' => Hash::make('password'),
            'role' => 'SUPER_ADMIN',
            'aktif' => true,
        ]);

        // Run other seeders
        $this->call([
            MemberSeeder::class,
            TransactionSeeder::class,
            MachineSeeder::class,
        ]);
    }
}
