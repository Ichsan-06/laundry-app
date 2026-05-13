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
    public const ROLE_OWNER = 'Owner';
    public const ROLE_KASIR = 'Kasir';
    public const ROLE_MANAGER = 'Manager';
    public const ROLE_OPERATOR = 'Operator';

    public const LEGACY_ROLE_MAP = [
        self::ROLE_SUPER_ADMIN => 'SUPER_ADMIN',
        self::ROLE_OWNER => 'ADMIN',
        self::ROLE_KASIR => 'KASIR',
        self::ROLE_MANAGER => 'KASIR',
        self::ROLE_OPERATOR => 'KASIR',
    ];

    protected string $guard_name = 'web';

    protected $fillable = [
        'outlet_id',
        'tenant_id',
        'nama',
        'email',
        'password_hash',
        'role',
        'user_type',
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

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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
            self::ROLE_OWNER,
            self::ROLE_KASIR,
            self::ROLE_MANAGER,
            self::ROLE_OPERATOR,
        ];
    }

    public static function legacyRoleFromRoleName(string $roleName): string
    {
        return self::LEGACY_ROLE_MAP[$roleName] ?? 'KASIR';
    }

    public function activeSubscription()
    {
        return $this->tenant?->activeSubscription;
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin' || $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isOwner(): bool
    {
        return $this->user_type === 'owner' || $this->hasRole(self::ROLE_OWNER);
    }

    public function isStaff(): bool
    {
        return $this->user_type === 'staff';
    }

    public function accessibleOutletIds(): array
    {
        if ($this->isSuperAdmin()) {
            return Outlet::query()->pluck('id')->all();
        }

        if ($this->isOwner()) {
            return Outlet::query()
                ->where('tenant_id', $this->tenant_id)
                ->pluck('id')
                ->all();
        }

        return array_values(array_filter([$this->outlet_id]));
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
