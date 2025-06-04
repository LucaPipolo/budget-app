<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Accounts;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Accounts\StoreAccountRequest;
use App\Http\Requests\Api\V1\Accounts\UpdateAccountRequest;
use App\Http\Resources\Api\V1\Accounts\AccountResource;
use App\Http\Resources\Api\V1\Transactions\TransactionResource;
use App\Models\Account;
use App\Models\Merchant;
use App\Policies\AccountPolicy;
use App\Traits\HasIncludedResources;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class AccountsApiController extends ApiController
{
    use HasIncludedResources;

    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = AccountPolicy::class;

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
            'transactions' => ['transactions', TransactionResource::class],
        ];
    }

    /**
     * List accounts.
     *
     * @return AnonymousResourceCollection The list of accounts.
     *
     * @response AnonymousResourceCollection<Illuminate\Pagination\LengthAwarePaginator<AccountResource>>
     *
     * @throws TokenAbilitiesException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->isAble('viewAny', Account::class, 'read');

        $user = auth()->user();
        $teamIds = $user->allTeams()->pluck('id');

        /** @var Collection<int, Account> $accounts */
        $accounts = QueryBuilder::for(Account::class)
            ->allowedIncludes(['transactions'])
            ->allowedFilters([
                'name',
                'iban',
                'swift',
                AllowedFilter::exact('teamId', 'team_id')->ignore(null),
                AllowedFilter::exact('type', 'type')->ignore(null),
                AllowedFilter::exact('origin', 'origin')->ignore(null),
                AllowedFilter::exact('currency', 'currency')->ignore(null),
                AllowedFilter::operator('balance', FilterOperator::DYNAMIC, 'or'),
            ])
            ->allowedSorts([
                'name',
                'balance',
                AllowedSort::field('createdAt', 'created_at'),
                AllowedSort::field('updatedAt', 'updated_at'),
            ])
            ->where(function (Builder $query) use ($teamIds): void {
                $query->whereIn('team_id', $teamIds);
            })
            ->paginate();

        return AccountResource::collection($accounts);
    }

    /**
     * Create account.
     *
     * @param  StoreAccountRequest  $request  The request containing the account data.
     *
     * @return AccountResource The created account or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function store(StoreAccountRequest $request): AccountResource
    {
        $this->isAble('create', new Merchant([
            'team_id' => $request->input('data.attributes.teamId'),
        ]), 'create');

        $account = Account::create(
            array_merge(
                ['origin' => 'api'],
                $request->mappedAttributes(),
            )
        );

        return new AccountResource($account);
    }

    /**
     * Show account.
     *
     * @param  string  $account_id  The ID of the account to display.
     *
     * @throws TokenAbilitiesException
     */
    public function show(string $account_id): AccountResource
    {
        /** @var Account $account */
        $account = QueryBuilder::for(Account::class)
            ->allowedIncludes(['transactions'])
            ->findOrFail($account_id);

        $this->isAble('view', $account, 'read');

        return new AccountResource($account);
    }

    /**
     * Replace account.
     *
     * @param  StoreAccountRequest  $request  The request containing the updated account data.
     * @param  string  $account_id  The ID of the account to update.
     *
     * @return AccountResource The updated account or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function replace(StoreAccountRequest $request, string $account_id): AccountResource
    {
        $account = Account::findOrFail($account_id);
        $account->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $account, 'update');

        $attributes = array_merge(
            [
                'origin' => 'api',
                'balance' => '0',
            ],
            $request->mappedAttributes()
        );
        $account->update($attributes);

        return new AccountResource($account);
    }

    /**
     * Update account.
     *
     * @param  UpdateAccountRequest  $request  The request containing the updated account data.
     * @param  string  $account_id  The ID of the account to update.
     *
     * @return AccountResource|JsonResponse The updated account or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function update(UpdateAccountRequest $request, string $account_id): AccountResource|JsonResponse
    {
        $account = Account::findOrFail($account_id);
        $request->has('data.attributes.teamId') && $account->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $account, 'update');

        $account->update(
            array_merge(
                ['origin' => 'api'],
                $request->mappedAttributes(),
            )
        );

        return new AccountResource($account);
    }

    /**
     * Delete account.
     *
     * @param  string  $account_id  The ID of the account to delete.
     *
     * @return JsonResponse The response indicating the result of the operation.
     *
     * @throws TokenAbilitiesException
     */
    public function destroy(string $account_id): JsonResponse
    {
        $account = Account::findOrFail($account_id);
        $this->isAble('delete', $account, 'delete');

        $account->delete();

        return response()->json([], 204);
    }
}
