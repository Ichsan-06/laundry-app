<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class UserPolicy
{
    use HandlesTenantAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('staff.view') || $user->can('users.manage');
    }

    public function view(User $user, User $model): bool
    {
        if ($user->tenant_id !== $model->tenant_id && ! $user->isSuperAdmin()) {
            return false;
        }

        if ($model->isOwner() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->can('staff.view') || $user->can('users.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('staff.create') || $user->can('users.manage');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->tenant_id !== $model->tenant_id && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->can('staff.update') || $user->can('users.manage');
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->tenant_id !== $model->tenant_id && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->can('staff.delete') || $user->can('users.manage');
    }
}
