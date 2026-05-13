<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('wijayapay_merchant_code')->nullable()->after('aktif');
            $table->text('wijayapay_api_key')->nullable()->after('wijayapay_merchant_code');
            $table->string('wijayapay_create_url')->nullable()->after('wijayapay_api_key');
            $table->string('wijayapay_status_url')->nullable()->after('wijayapay_create_url');
        });
    }

    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn([
                'wijayapay_merchant_code',
                'wijayapay_api_key',
                'wijayapay_create_url',
                'wijayapay_status_url',
                'wijayapay_callback_url',
            ]);
        });
    }
};
