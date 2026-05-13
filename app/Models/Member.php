<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'outlet_id',
        'id_member',
        'nama',
        'no_hp',
        'email',
        'saldo',
        'status',
        'tanggal_daftar',
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'saldo' => 'decimal:2',
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

    // before creating status always set to active
    protected static function booted()
    {
        static::creating(function ($member) {
            $member->status = 'active';
        });
    }
}
