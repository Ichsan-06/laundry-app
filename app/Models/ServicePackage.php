<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServicePackage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'outlet_id',
        'nama_paket',
        'deskripsi',
        'harga_per_kg',
        'berat_minimal',
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
}
