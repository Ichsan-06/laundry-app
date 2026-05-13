<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends \Spatie\Permission\Models\Role
{
    protected $fillable = [
        'name',
        'guard_name',
        'tenant_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant($query, ?string $tenantId)
    {
        return $query->where(function ($roleQuery) use ($tenantId) {
            $roleQuery->whereNull('tenant_id');

            if ($tenantId) {
                $roleQuery->orWhere('tenant_id', $tenantId);
            }
        });
    }
}
