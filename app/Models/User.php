<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasUuids;

    public const ROLE_SUPER_ADMIN = 'Super Admin';
    public const ROLE_ADMIN = 'Admin';
    public const ROLE_USER = 'User';

    public const LEGACY_ROLE_MAP = [
        self::ROLE_SUPER_ADMIN => 'SUPER_ADMIN',
        self::ROLE_ADMIN => 'ADMIN',
        self::ROLE_USER => 'KASIR',
    ];

    protected string $guard_name = 'web';

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

    protected $appends = [
        'display_role',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function getDisplayRoleAttribute(): string
    {
        return $this->getRoleNames()->first() ?? 'Tanpa Role';
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

    public static function availableRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_USER,
        ];
    }

    public static function legacyRoleFromRoleName(string $roleName): string
    {
        return self::LEGACY_ROLE_MAP[$roleName] ?? 'KASIR';
    }

    public function syncLegacyRoleColumn(): void
    {
        $primaryRole = $this->getRoleNames()->first();

        if (! $primaryRole) {
            return;
        }

        $this->forceFill([
            'role' => self::legacyRoleFromRoleName($primaryRole),
        ])->saveQuietly();
    }
}
