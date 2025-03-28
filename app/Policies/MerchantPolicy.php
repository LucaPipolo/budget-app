<?php

declare(strict_types=1);

namespace App\Policies;

use App\Exceptions\InvalidRelationshipException;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool True if the user is authorized to view the merchant.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Merchant  $merchant  The requested merchant.
     *
     * @return bool True if the user is authorized to view the requested merchant.
     */
    public function view(User $user, Merchant $merchant): bool
    {
        return $user->merchants()->where('merchants.id', $merchant->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  The logged-in user.
     * @param  ?Merchant  $merchant  The requested merchant.
     *
     * @return bool True if the user is authorized to create merchant associated to the given team ID.
     *
     * @throws InvalidRelationshipException When the user tries to create a merchant for a team they don't belong to.
     */
    public function create(User $user, ?Merchant $merchant = null): bool
    {
        if ($merchant === null) {
            return $user->allTeams()->isNotEmpty();
        }

        if (! $user->allTeams()->contains($merchant->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Merchant  $merchant  The requested merchant.
     *
     * @return bool True if the user is authorized to update the requested merchant.
     *
     * @throws InvalidRelationshipException When the user tries to update a merchant to a team they don't belong to.
     */
    public function update(User $user, Merchant $merchant): bool
    {
        if (! $user->merchants()->where('merchants.id', $merchant->id)->exists()) {
            return false;
        }

        if (! $user->allTeams()->contains($merchant->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Merchant  $merchant  The requested merchant.
     *
     * @return bool True if the user is authorized to delete the requested merchant.
     */
    public function delete(User $user, Merchant $merchant): bool
    {
        return $user->merchants()->where('merchants.id', $merchant->id)->exists();
    }
}
