<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory, HasUuids;

    public const UNIT_OPTIONS = [
        'pcs' => 'pcs',
        'pack' => 'pack',
        'botol' => 'botol',
        'sachet' => 'sachet',
        'kg' => 'kg',
        'liter' => 'liter',
        'meter' => 'meter',
    ];

    protected $fillable = [
        'outlet_id',
        'nama_barang',
        'satuan',
        'stok',
        'alert_stok',
        'catatan',
    ];

    protected $casts = [
        'stok' => 'decimal:2',
        'alert_stok' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(InventoryStockMovement::class)->latest('created_at');
    }

    public function isLowStock(): bool
    {
        return (float) $this->stok <= (float) $this->alert_stok;
    }

    public function formattedQuantity(float|int|string|null $value = null): string
    {
        $quantity = (float) ($value ?? $this->stok);
        $formatted = number_format($quantity, 2, ',', '.');
        $formatted = rtrim(rtrim($formatted, '0'), ',');

        return $formatted;
    }
}
