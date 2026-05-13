<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class MemberPolicy
{
    use HandlesTenantAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    public function view(User $user, Member $member): bool
    {
        return $user->can('customers.view')
            && $this->belongsToAccessibleOutlet($user, $member->outlet_id);
    }

    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    public function update(User $user, Member $member): bool
    {
        return $user->can('customers.update')
            && $this->belongsToAccessibleOutlet($user, $member->outlet_id);
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->can('customers.delete')
            && $this->belongsToAccessibleOutlet($user, $member->outlet_id);
    }
}
