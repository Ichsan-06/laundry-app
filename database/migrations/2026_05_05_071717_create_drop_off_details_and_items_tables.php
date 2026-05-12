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
        // Many-to-Many for Services
        Schema::create('transaction_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignUuid('service_package_id')->constrained('service_packages')->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });

        // Many-to-Many for Addons
        Schema::create('transaction_addons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignUuid('addon_option_id')->constrained('addon_options')->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });

        // Items List
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->string('nama_item');
            $table->integer('qty')->default(1);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transaction_addons');
        Schema::dropIfExists('transaction_services');
    }
};
