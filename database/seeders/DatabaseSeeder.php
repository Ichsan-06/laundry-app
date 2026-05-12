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
        $outlet = Outlet::firstOrCreate([
            'nama_outlet' => 'Laundry Express Utama',
        ], [
            'nama_outlet' => 'Laundry Express Utama',
            'alamat' => 'Jl. Merdeka No. 123',
            'telepon' => '021-1234567',
            'kota' => 'Jakarta',
            'aktif' => true,
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $superAdmin = User::updateOrCreate([
            'email' => 'superadmin@laundry.com',
        ], [
            'outlet_id' => $outlet->id,
            'nama' => 'Super Admin',
            'email' => 'superadmin@laundry.com',
            'password_hash' => Hash::make('password'),
            'role' => 'SUPER_ADMIN',
            'aktif' => true,
        ]);
        $superAdmin->syncRoles([User::ROLE_SUPER_ADMIN]);
        $superAdmin->syncLegacyRoleColumn();

        $admin = User::updateOrCreate([
            'email' => 'admin@laundry.com',
        ], [
            'outlet_id' => $outlet->id,
            'nama' => 'Admin Outlet',
            'email' => 'admin@laundry.com',
            'password_hash' => Hash::make('password'),
            'role' => 'ADMIN',
            'aktif' => true,
        ]);
        $admin->syncRoles([User::ROLE_ADMIN]);
        $admin->syncLegacyRoleColumn();

        $user = User::updateOrCreate([
            'email' => 'user@laundry.com',
        ], [
            'outlet_id' => $outlet->id,
            'nama' => 'Kasir User',
            'email' => 'user@laundry.com',
            'password_hash' => Hash::make('password'),
            'role' => 'KASIR',
            'aktif' => true,
        ]);
        $user->syncRoles([User::ROLE_USER]);
        $user->syncLegacyRoleColumn();

        $this->call([
            MemberSeeder::class,
            TransactionSeeder::class,
            MachineSeeder::class,
        ]);
    }
}
