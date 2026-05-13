<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'tenant_id',
        'nama_outlet',
        'alamat',
        'telepon',
        'kota',
        'aktif',
        'wijayapay_merchant_code',
        'wijayapay_api_key',
        'wijayapay_create_url',
        'wijayapay_status_url',
        'wijayapay_callback_url',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
