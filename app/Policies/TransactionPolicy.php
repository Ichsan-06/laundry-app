<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class TransactionPolicy
{
    use HandlesTenantAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('transactions.view');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.view')
            && $this->belongsToAccessibleOutlet($user, $transaction->outlet_id);
    }

    public function create(User $user): bool
    {
        return $user->can('transactions.create');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.update')
            && $this->belongsToAccessibleOutlet($user, $transaction->outlet_id);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.delete')
            && $this->belongsToAccessibleOutlet($user, $transaction->outlet_id);
    }
}
