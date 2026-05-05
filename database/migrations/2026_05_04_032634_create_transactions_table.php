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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignUuid('cashier_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('member_id')->nullable()->constrained('members')->nullOnDelete();
            
            $table->string('transaction_number')->unique();
            $table->enum('service_type', ['WASH_ONLY', 'DRY_ONLY', 'WASH_DRY', 'IRONING', 'COMPLETE'])->default('COMPLETE');
            $table->enum('status', ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('member_discount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->enum('payment_method', ['CASH', 'TRANSFER', 'E_WALLET', 'QRIS'])->default('CASH');
            $table->decimal('amount_received', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
