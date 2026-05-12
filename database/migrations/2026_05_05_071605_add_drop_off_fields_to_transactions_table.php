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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_type')->default('SELF_SERVICE')->after('transaction_number');
            $table->decimal('weight', 8, 2)->nullable()->after('service_type');
            $table->timestamp('estimated_finish')->nullable()->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_type', 'weight', 'estimated_finish']);
        });
    }
};
