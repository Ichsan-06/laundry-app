<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait HandlesTenantAuthorization
{
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    protected function belongsToAccessibleOutlet(User $user, string $outletId): bool
    {
        return in_array($outletId, $user->accessibleOutletIds(), true);
    }
}
