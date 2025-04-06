<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teams;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Teams\StoreTeamRequest;
use App\Http\Resources\Api\V1\Accounts\AccountResource;
use App\Http\Resources\Api\V1\Merchants\MerchantResource;
use App\Http\Resources\Api\V1\Teams\TeamResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Team;
use App\Policies\TeamPolicy;
use App\Traits\HasIncludedResources;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TeamsApiController extends ApiController
{
    use HasIncludedResources;

    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TeamPolicy::class;

    /**
     * Included resources.
     */
    protected array $includedResources = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->includedResources = [
            'users' => [
                function (Team $team) {
                    return $team->allUsers()->merge([$team->owner])->unique('id');
                },
                UserResource::class,
            ],
            'accounts' => ['accounts', AccountResource::class],
            'merchants' => ['merchants', MerchantResource::class],
        ];
    }

    /**
     * List teams.
     *
     * @return AnonymousResourceCollection The list of teams.
     *
     * @response AnonymousResourceCollection<Illuminate\Pagination\LengthAwarePaginator<TeamResource>>
     *
     * @throws TokenAbilitiesException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->isAble('viewAny', Team::class, 'read');

        $user = auth()->user();
        $teamIds = $user->allTeams()->pluck('id');

        /** @var Collection<int, Team> $teams */
        $teams = QueryBuilder::for(Team::class)
            ->allowedIncludes(['users', 'accounts', 'merchants'])
            ->allowedFilters(['name'])
            ->allowedSorts([
                'name',
                AllowedSort::field('createdAt', 'created_at'),
                AllowedSort::field('updatedAt', 'updated_at'),
            ])
            ->where(function (Builder $query) use ($teamIds): void {
                $query->whereIn('id', $teamIds);
            })
            ->get();

        $included = $this->prepareIncludedResources($teams, $this->includedResources);

        return TeamResource::collection($teams)
            ->additional($included->isNotEmpty() ? ['included' => $included] : []);
    }

    /**
     * Create team.
     *
     * @param  StoreTeamRequest  $request  The request containing the team data.
     *
     * @return TeamResource The created team or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function store(StoreTeamRequest $request): TeamResource
    {
        $this->isAble('create', Team::class, 'create');

        $user = auth()->user();
        $team = Team::create([
            'name' => $request->input('data.attributes.name'),
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        auth()->user()->teams()->attach($team->id, ['role' => 'admin']);

        return new TeamResource($team);
    }

    /**
     * Show team.
     *
     * @param  string  $team_id  The ID of the team to display.
     *
     * @throws TokenAbilitiesException
     */
    public function show(string $team_id): TeamResource
    {
        /** @var Team $team */
        $team = QueryBuilder::for(Team::class)
            ->allowedIncludes(['users', 'accounts', 'merchants'])
            ->findOrFail($team_id);

        $this->isAble('view', $team, 'read');

        $included = $this->prepareIncludedResources($team, $this->includedResources);

        return new TeamResource($team)
            ->additional($included->isNotEmpty() ? ['included' => $included] : []);
    }

    /**
     * Replace team.
     *
     * @param  StoreTeamRequest  $request  The request containing the updated team data.
     * @param  string  $team_id  The ID of the team to update.
     *
     * @return TeamResource The updated team or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function replace(StoreTeamRequest $request, string $team_id): TeamResource
    {
        $team = Team::findOrFail($team_id);
        $this->isAble('update', $team, 'update');

        $user = auth()->user();
        $model = [
            'name' => $request->input('data.attributes.name'),
            'user_id' => $user->id,
            'personal_team' => true,
        ];

        $team->update($model);

        return new TeamResource($team);
    }

    /**
     * Delete team.
     *
     * @param  string  $team_id  The ID of the team to delete.
     *
     * @return JsonResponse The response indicating the result of the operation.
     *
     * @throws TokenAbilitiesException
     */
    public function destroy(string $team_id): JsonResponse
    {
        $team = Team::findOrFail($team_id);
        $this->isAble('delete', $team, 'delete');

        $team->delete();

        return response()->json([], 204);
    }
}
