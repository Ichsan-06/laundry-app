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
        'service_type',
        'status',
        'subtotal',
        'member_discount',
        'total_amount',
        'payment_method',
        'amount_received',
        'change_amount',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'member_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
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
}
