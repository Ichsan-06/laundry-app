<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('payment_method');
            $table->string('trx_reference')->nullable()->after('payment_status');
            $table->string('ref_id')->nullable()->unique()->after('trx_reference');
            $table->decimal('payment_fee', 15, 2)->default(0)->after('ref_id');
            $table->timestamp('payment_expires_at')->nullable()->after('payment_fee');
            $table->timestamp('paid_at')->nullable()->after('payment_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'trx_reference',
                'ref_id',
                'payment_fee',
                'payment_expires_at',
                'paid_at',
            ]);
        });
    }
};
