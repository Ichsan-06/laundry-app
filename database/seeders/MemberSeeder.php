<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Outlet;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::first();

        $members = [
            [
                'outlet_id' => $outlet->id,
                'id_member' => 'MEM-001',
                'nama' => 'John Doe',
                'no_hp' => '081234567890',
                'email' => 'john@example.com',
                'status' => 'PREMIUM',
                'tanggal_daftar' => now(),
            ],
            [
                'outlet_id' => $outlet->id,
                'id_member' => 'MEM-002',
                'nama' => 'Jane Smith',
                'no_hp' => '081298765432',
                'email' => 'jane@example.com',
                'status' => 'ACTIVE',
                'tanggal_daftar' => now(),
            ],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
