<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->string('machine_code')->unique();
            $table->enum('machine_type', ['WASHER', 'DRYER'])->default('WASHER');
            $table->decimal('capacity_kg', 5, 2)->default(0);
            $table->enum('status', ['AVAILABLE', 'IN_USE', 'MAINTENANCE', 'FAULTY'])->default('AVAILABLE');
            $table->string('brand')->nullable();
            $table->timestamp('last_serviced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
