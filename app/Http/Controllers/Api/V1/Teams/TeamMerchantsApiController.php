<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teams;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Merchants\MerchantResource;
use App\Http\Resources\Api\V1\Teams\TeamMerchantsResource;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamMerchantsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TeamPolicy::class;

    /**
     * List teams' merchants relationships.
     *
     * @return TeamMerchantsResource The list of teams' merchants relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function merchantsRelationships(Team $team): TeamMerchantsResource
    {
        $this->isAble('view', $team, 'read');

        return new TeamMerchantsResource($team);
    }

    /**
     * List teams' merchants.
     *
     * @return AnonymousResourceCollection The list of teams' merchants.
     *
     * @throws TokenAbilitiesException
     */
    public function merchants(Team $team): AnonymousResourceCollection
    {
        $this->isAble('view', $team, 'read');

        return MerchantResource::collection($team->merchants);
    }
}
