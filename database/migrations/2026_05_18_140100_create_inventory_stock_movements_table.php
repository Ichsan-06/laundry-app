<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignUuid('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['IN', 'OUT']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('stock_before', 12, 2);
            $table->decimal('stock_after', 12, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_movements');
    }
};
