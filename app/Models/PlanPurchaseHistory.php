<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPurchaseHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'subscription_plan_id',
        'activated_subscription_id',
        'plan_name_snapshot',
        'amount',
        'payment_fee',
        'status',
        'payment_method',
        'payment_name',
        'ref_id',
        'trx_reference',
        'qr_image',
        'tutorial_pembayaran',
        'payment_expires_at',
        'paid_at',
        'last_payload',
    ];

    protected $casts = [
        'payment_expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'last_payload' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function activatedSubscription()
    {
        return $this->belongsTo(TenantSubscription::class, 'activated_subscription_id');
    }
}
