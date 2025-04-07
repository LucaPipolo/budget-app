<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teams;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Accounts\AccountResource;
use App\Http\Resources\Api\V1\Teams\TeamAccountsResource;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamAccountsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TeamPolicy::class;

    /**
     * List teams' accounts relationships.
     *
     * @return TeamAccountsResource The list of teams' accounts relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function accountsRelationships(Team $team): TeamAccountsResource
    {
        $this->isAble('view', $team, 'read');

        return new TeamAccountsResource($team);
    }

    /**
     * List teams' accounts.
     *
     * @return AnonymousResourceCollection The list of teams' accounts.
     *
     * @throws TokenAbilitiesException
     */
    public function accounts(Team $team): AnonymousResourceCollection
    {
        $this->isAble('view', $team, 'read');

        return AccountResource::collection($team->accounts);
    }
}
