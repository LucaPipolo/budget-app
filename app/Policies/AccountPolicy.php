<?php

declare(strict_types=1);

namespace App\Policies;

use App\Exceptions\InvalidRelationshipException;
use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool True if the user is authorized to view the account.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Account  $account  The requested account.
     *
     * @return bool True if the user is authorized to view the requested account.
     */
    public function view(User $user, Account $account): bool
    {
        return $user->accounts()->where('accounts.id', $account->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  The logged-in user.
     * @param  ?Account  $account  The requested account.
     *
     * @return bool True if the user is authorized to create account associated to the given team ID.
     *
     * @throws InvalidRelationshipException When the user tries to create a account for a team they don't belong to.
     */
    public function create(User $user, ?Account $account = null): bool
    {
        if ($account === null) {
            return $user->allTeams()->isNotEmpty();
        }

        if (! $user->allTeams()->contains($account->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Account  $account  The requested account.
     *
     * @return bool True if the user is authorized to update the requested account.
     *
     * @throws InvalidRelationshipException When the user tries to update a account to a team they don't belong to.
     */
    public function update(User $user, Account $account): bool
    {
        if (! $user->accounts()->where('accounts.id', $account->id)->exists()) {
            return false;
        }

        if (! $user->allTeams()->contains($account->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Account  $account  The requested account.
     *
     * @return bool True if the user is authorized to delete the requested account.
     */
    public function delete(User $user, Account $account): bool
    {
        return $user->accounts()->where('accounts.id', $account->id)->exists();
    }
}
