<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outcome extends Model
{
    use HasFactory, HasUuids;

    public const CATEGORY_OPTIONS = [
        'operasional' => 'Operasional (Listrik, Air, dll)',
        'gaji_upah' => 'Gaji & Upah',
        'maintenance_service' => 'Maintenance & Service',
        'sewa_tempat' => 'Sewa Tempat',
        'marketing_iklan' => 'Marketing & Iklan',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'outlet_id',
        'user_id',
        'kategori',
        'tanggal',
        'deskripsi',
        'jumlah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORY_OPTIONS[$this->kategori] ?? ucfirst(str_replace('_', ' ', $this->kategori));
    }
}
