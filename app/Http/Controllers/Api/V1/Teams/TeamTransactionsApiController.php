<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teams;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Teams\TeamTransactionsResource;
use App\Http\Resources\Api\V1\Transactions\TransactionResource;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamTransactionsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TeamPolicy::class;

    /**
     * List teams' transaction relationships.
     *
     * @return TeamTransactionsResource The list of teams' transaction relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function transactionsRelationships(Team $team): TeamTransactionsResource
    {
        $this->isAble('view', $team, 'read');

        return new TeamTransactionsResource($team);
    }

    /**
     * List teams' transactions.
     *
     * @return AnonymousResourceCollection The list of teams' transactions.
     *
     * @throws TokenAbilitiesException
     */
    public function transactions(Team $team): AnonymousResourceCollection
    {
        $this->isAble('view', $team, 'read');

        return TransactionResource::collection($team->transactions);
    }
}
