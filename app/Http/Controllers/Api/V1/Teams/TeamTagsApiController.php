<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teams;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Tags\TagResource;
use App\Http\Resources\Api\V1\Teams\TeamTagsResource;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamTagsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TeamPolicy::class;

    /**
     * List teams' tags relationships.
     *
     * @return TeamTagsResource The list of teams' tags relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function tagsRelationships(Team $team): TeamTagsResource
    {
        $this->isAble('view', $team, 'read');

        return new TeamTagsResource($team);
    }

    /**
     * List teams' tags.
     *
     * @return AnonymousResourceCollection The list of teams' tags.
     *
     * @throws TokenAbilitiesException
     */
    public function tags(Team $team): AnonymousResourceCollection
    {
        $this->isAble('view', $team, 'read');

        return TagResource::collection($team->tags);
    }
}
