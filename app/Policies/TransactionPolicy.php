<?php

declare(strict_types=1);

namespace App\Policies;

use App\Exceptions\InvalidRelationshipException;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool True if the user is authorized to view the transaction.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Transaction  $transaction  The requested transaction.
     *
     * @return bool True if the user is authorized to view the requested transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->transactions()->where('transactions.id', $transaction->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  The logged-in user.
     * @param  ?Transaction  $transaction  The requested transaction.
     *
     * @return bool True if the user is authorized to create transaction associated to the given team ID.
     *
     * @throws InvalidRelationshipException When the user tries to create a transaction for a team they don't belong to.
     */
    public function create(User $user, ?Transaction $transaction = null): bool
    {
        if ($transaction === null) {
            return $user->allTeams()->isNotEmpty();
        }

        if (! $user->allTeams()->contains($transaction->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Transaction  $transaction  The requested transaction.
     *
     * @return bool True if the user is authorized to update the requested transaction.
     *
     * @throws InvalidRelationshipException When the user tries to update a transaction to a team they don't belong to.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        if (! $user->transactions()->where('transactions.id', $transaction->id)->exists()) {
            return false;
        }

        if (! $user->allTeams()->contains($transaction->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Transaction  $transaction  The requested transaction.
     *
     * @return bool True if the user is authorized to delete the requested transaction.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->transactions()->where('transactions.id', $transaction->id)->exists();
    }
}
