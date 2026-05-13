<?php

namespace App\Policies;

use App\Models\Outlet;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class OutletPolicy
{
    use HandlesTenantAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('outlets.view');
    }

    public function view(User $user, Outlet $outlet): bool
    {
        return $user->can('outlets.view')
            && $this->belongsToAccessibleOutlet($user, $outlet->id);
    }

    public function create(User $user): bool
    {
        return $user->can('outlets.create');
    }

    public function update(User $user, Outlet $outlet): bool
    {
        return $user->can('outlets.update')
            && $this->belongsToAccessibleOutlet($user, $outlet->id);
    }

    public function delete(User $user, Outlet $outlet): bool
    {
        return $user->can('outlets.delete')
            && $this->belongsToAccessibleOutlet($user, $outlet->id);
    }
}
