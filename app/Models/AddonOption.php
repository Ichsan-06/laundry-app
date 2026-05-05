<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AddonOption extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'outlet_id',
        'nama',
        'deskripsi',
        'harga',
        'aktif',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
