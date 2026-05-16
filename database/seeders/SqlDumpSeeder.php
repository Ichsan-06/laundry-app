<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SqlDumpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('laundry_app.dump');
        
        if (!File::exists($path)) {
            $this->command->error("File dump tidak ditemukan di: {$path}");
            return;
        }

        $this->command->info('Sedang mengimpor dump SQL...');
        
        try {
            // Menggunakan DB::unprepared untuk menjalankan SQL mentah dari file
            // Ini akan mengeksekusi semua statement termasuk CREATE, DROP, dan INSERT
            DB::unprepared(File::get($path));
            $this->command->info('Impor dump SQL berhasil dilakukan.');
        } catch (\Exception $e) {
            $this->command->error('Terjadi kesalahan saat mengimpor dump: ' . $e->getMessage());
        }
    }
}
