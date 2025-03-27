<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teams;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Teams\TeamUsersResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamUsersApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TeamPolicy::class;

    /**
     * List teams' users relationships.
     *
     * @return TeamUsersResource The list of teams' users relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function usersRelationships(Team $team): TeamUsersResource
    {
        $this->isAble('view', $team, 'read');

        return new TeamUsersResource($team);
    }

    /**
     * List teams' users.
     *
     * @return AnonymousResourceCollection The list of teams' users.
     *
     * @throws TokenAbilitiesException
     */
    public function users(Team $team): AnonymousResourceCollection
    {
        $this->isAble('view', $team, 'read');

        return UserResource::collection($team->allUsers());
    }
}
