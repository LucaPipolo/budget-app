<?php

declare(strict_types=1);

namespace App\Policies;

use App\Exceptions\InvalidRelationshipException;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool True if the user is authorized to view the category.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Category  $category  The requested category.
     *
     * @return bool True if the user is authorized to view the requested category.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->categories()->where('categories.id', $category->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  The logged-in user.
     * @param  ?Category  $category  The requested category.
     *
     * @return bool True if the user is authorized to create category associated to the given team ID.
     *
     * @throws InvalidRelationshipException When the user tries to create a category for a team they don't belong to.
     */
    public function create(User $user, ?Category $category = null): bool
    {
        if ($category === null) {
            return $user->allTeams()->isNotEmpty();
        }

        if (! $user->allTeams()->contains($category->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Category  $category  The requested category.
     *
     * @return bool True if the user is authorized to update the requested category.
     *
     * @throws InvalidRelationshipException When the user tries to update a category to a team they don't belong to.
     */
    public function update(User $user, Category $category): bool
    {
        if (! $user->categories()->where('categories.id', $category->id)->exists()) {
            return false;
        }

        if (! $user->allTeams()->contains($category->team_id)) {
            throw new InvalidRelationshipException();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user  The logged-in user.
     * @param  Category  $category  The requested category.
     *
     * @return bool True if the user is authorized to delete the requested category.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->categories()->where('categories.id', $category->id)->exists();
    }
}
