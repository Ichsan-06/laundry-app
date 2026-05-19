<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outcomes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kategori');
            $table->date('tanggal');
            $table->text('deskripsi');
            $table->decimal('jumlah', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};
