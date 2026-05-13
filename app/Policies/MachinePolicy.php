<?php

namespace App\Policies;

use App\Models\Machine;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class MachinePolicy
{
    use HandlesTenantAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('machines.view');
    }

    public function view(User $user, Machine $machine): bool
    {
        return $user->can('machines.view')
            && $this->belongsToAccessibleOutlet($user, $machine->outlet_id);
    }

    public function create(User $user): bool
    {
        return $user->can('machines.create');
    }

    public function update(User $user, Machine $machine): bool
    {
        return $user->can('machines.update')
            && $this->belongsToAccessibleOutlet($user, $machine->outlet_id);
    }

    public function delete(User $user, Machine $machine): bool
    {
        return $user->can('machines.delete')
            && $this->belongsToAccessibleOutlet($user, $machine->outlet_id);
    }
}
