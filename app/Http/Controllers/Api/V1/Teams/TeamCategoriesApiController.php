<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teams;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Categories\CategoryResource;
use App\Http\Resources\Api\V1\Teams\TeamCategoriesResource;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamCategoriesApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TeamPolicy::class;

    /**
     * List teams' categories relationships.
     *
     * @return TeamCategoriesResource The list of teams' categories relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function categoriesRelationships(Team $team): TeamCategoriesResource
    {
        $this->isAble('view', $team, 'read');

        return new TeamCategoriesResource($team);
    }

    /**
     * List teams' categories.
     *
     * @return AnonymousResourceCollection The list of teams' categories.
     *
     * @throws TokenAbilitiesException
     */
    public function categories(Team $team): AnonymousResourceCollection
    {
        $this->isAble('view', $team, 'read');

        return CategoryResource::collection($team->categories);
    }
}
