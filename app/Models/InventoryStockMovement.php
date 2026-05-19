<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryStockMovement extends Model
{
    use HasFactory, HasUuids;

    public const TYPE_IN = 'IN';
    public const TYPE_OUT = 'OUT';

    protected $fillable = [
        'inventory_item_id',
        'outlet_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'catatan',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
