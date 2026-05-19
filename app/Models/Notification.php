<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'transaction_id',
        'title',
        'message',
        'is_read',
        'type',
        'tenant_id',
        'outlet_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
