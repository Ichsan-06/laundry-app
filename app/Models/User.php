<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'outlet_id',
        'nama',
        'email',
        'password_hash',
        'role',
        'aktif',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    // Override password attribute for authentication if needed
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
