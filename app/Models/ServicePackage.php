<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServicePackage extends Model
{
    use HasFactory, HasUuids;

    public const SATUAN_PER_KG = 'per_kg';
    public const SATUAN_PER_PASANG = 'per_pasang';
    public const SATUAN_PER_PCS = 'per_pcs';
    public const SATUAN_PER_METER = 'per_meter';

    public const SATUAN_OPTIONS = [
        self::SATUAN_PER_KG => 'Per Kg',
        self::SATUAN_PER_PASANG => 'Per Pasang',
        self::SATUAN_PER_PCS => 'Per Pcs',
        self::SATUAN_PER_METER => 'Per Meter',
    ];

    protected $fillable = [
        'outlet_id',
        'nama_paket',
        'deskripsi',
        'harga_per_kg',
        'berat_minimal',
        'satuan',
        'aktif',
    ];

    protected $casts = [
        'harga_per_kg' => 'decimal:2',
        'berat_minimal' => 'decimal:2',
        'aktif' => 'boolean',
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

    public function satuanLabel(): string
    {
        return self::SATUAN_OPTIONS[$this->satuan ?? self::SATUAN_PER_KG] ?? 'Per Kg';
    }

    public function satuanSingkat(): string
    {
        return match ($this->satuan ?? self::SATUAN_PER_KG) {
            self::SATUAN_PER_PASANG => 'pasang',
            self::SATUAN_PER_PCS => 'pcs',
            self::SATUAN_PER_METER => 'meter',
            default => 'kg',
        };
    }
}
