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
        Schema::create('self_service_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignUuid('machine_id')->constrained('machines')->onDelete('cascade');
            $table->foreignUuid('machine_duration_id')->constrained('machine_durations')->onDelete('cascade');
            
            $table->integer('duration_minutes')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('machine_status', ['RUNNING', 'COMPLETED', 'STOPPED'])->default('STOPPED');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_service_details');
    }
};
