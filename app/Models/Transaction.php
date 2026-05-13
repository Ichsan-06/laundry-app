<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'outlet_id',
        'cashier_id',
        'member_id',
        'transaction_number',
        'transaction_type',
        'service_type',
        'weight',
        'estimated_finish',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'status',
        'subtotal',
        'member_discount',
        'total_amount',
        'payment_method',
        'payment_status',
        'trx_reference',
        'ref_id',
        'payment_fee',
        'payment_expires_at',
        'paid_at',
        'amount_received',
        'change_amount',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'member_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'estimated_finish' => 'datetime',
        'payment_expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function tenant()
    {
        return $this->hasOneThrough(
            Tenant::class,
            Outlet::class,
            'id',
            'id',
            'outlet_id',
            'tenant_id',
        );
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function selfServiceDetails()
    {
        return $this->hasMany(SelfServiceDetail::class);
    }

    public function servicePackages()
    {
        return $this->belongsToMany(ServicePackage::class, 'transaction_services')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function addonOptions()
    {
        return $this->belongsToMany(AddonOption::class, 'transaction_addons')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
