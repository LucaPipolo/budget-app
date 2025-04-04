<?php

declare(strict_types=1);

namespace App\Policies;

use App\Exceptions\InvalidRelationshipException;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool True if the user is authorized to view the tag.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Tag  $tag  The requested tag.
     *
     * @return bool True if the user is authorized to view the requested tag.
     */
    public function view(User $user, Tag $tag): bool
    {
        return $user->tags()->where('tags.id', $tag->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  The logged-in user.
     * @param  ?Tag  $tag  The requested tag.
     *
     * @return bool True if the user is authorized to create tag associated to the given team ID.
     *
     * @throws InvalidRelationshipException When the user tries to create a tag for a team they don't belong to.
     */
    public function create(User $user, ?Tag $tag = null): bool
    {
        if ($tag === null) {
            return $user->allTeams()->isNotEmpty();
        }

        if (! $user->allTeams()->contains($tag->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Tag  $tag  The requested tag.
     *
     * @return bool True if the user is authorized to update the requested tag.
     *
     * @throws InvalidRelationshipException When the user tries to update a tag to a team they don't belong to.
     */
    public function update(User $user, Tag $tag): bool
    {
        if (! $user->tags()->where('tags.id', $tag->id)->exists()) {
            return false;
        }

        if (! $user->allTeams()->contains($tag->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Tag  $tag  The requested tag.
     *
     * @return bool True if the user is authorized to delete the requested tag.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $user->tags()->where('tags.id', $tag->id)->exists();
    }
}
