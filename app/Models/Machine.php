<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Machine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'outlet_id',
        'machine_code',
        'machine_type',
        'capacity_kg',
        'status',
        'brand',
        'last_serviced_at',
    ];

    protected $casts = [
        'last_serviced_at' => 'datetime',
        'capacity_kg' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function durations()
    {
        return $this->hasMany(MachineDuration::class);
    }
}
