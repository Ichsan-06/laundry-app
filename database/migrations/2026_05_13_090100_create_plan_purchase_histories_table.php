<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_purchase_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('subscription_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->foreignUuid('activated_subscription_id')->nullable()->constrained('tenant_subscriptions')->nullOnDelete();
            $table->string('plan_name_snapshot');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('payment_fee')->default(0);
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('QRIS');
            $table->string('payment_name')->nullable();
            $table->string('ref_id')->unique();
            $table->string('trx_reference')->nullable();
            $table->text('qr_image')->nullable();
            $table->text('tutorial_pembayaran')->nullable();
            $table->timestamp('payment_expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('last_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_purchase_histories');
    }
};
