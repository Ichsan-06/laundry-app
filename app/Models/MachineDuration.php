<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MachineDuration extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'machine_id',
        'duration_type',
        'duration_minutes',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
