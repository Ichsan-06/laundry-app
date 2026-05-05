<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SelfServiceDetail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'transaction_id',
        'machine_id',
        'machine_duration_id',
        'duration_minutes',
        'price',
        'start_time',
        'end_time',
        'machine_status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function machineDuration()
    {
        return $this->belongsTo(MachineDuration::class);
    }
}
