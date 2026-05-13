<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TenantContextService
{
    public function tenantId(?User $user): ?string
    {
        return $user?->tenant_id;
    }

    public function accessibleOutletIds(?User $user): array
    {
        return $user?->accessibleOutletIds() ?? [];
    }

    public function scopeByUser(Builder $query, ?User $user, string $outletColumn = 'outlet_id'): Builder
    {
        if (! $user || $user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereIn($outletColumn, $this->accessibleOutletIds($user));
    }
}
