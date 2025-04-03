<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Merchants;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Merchants\StoreMerchantRequest;
use App\Http\Requests\Api\V1\Merchants\UpdateMerchantRequest;
use App\Http\Resources\Api\V1\Merchants\MerchantResource;
use App\Models\Merchant;
use App\Policies\MerchantPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class MerchantsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = MerchantPolicy::class;

    /**
     * List merchants.
     *
     * @return AnonymousResourceCollection The list of merchants.
     *
     * @response AnonymousResourceCollection<Illuminate\Pagination\LengthAwarePaginator<MerchantResource>>
     *
     * @throws TokenAbilitiesException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->isAble('viewAny', Merchant::class, 'read');

        $user = auth()->user();
        $teamIds = $user->allTeams()->pluck('id');

        /** @var Collection<int, Merchant> $merchants */
        $merchants = QueryBuilder::for(Merchant::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('teamId', 'team_id')->ignore(null),
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

        return MerchantResource::collection($merchants);
    }

    /**
     * Create merchant.
     *
     * @param  StoreMerchantRequest  $request  The request containing the merchant data.
     *
     * @return MerchantResource The created merchant or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function store(StoreMerchantRequest $request): MerchantResource
    {
        $this->isAble('create', new Merchant([
            'team_id' => $request->input('data.attributes.teamId'),
        ]), 'create');

        $merchant = Merchant::create($request->mappedAttributes());

        return new MerchantResource($merchant);
    }

    /**
     * Show merchant.
     *
     * @param  string  $merchant_id  The ID of the merchant to display.
     *
     * @throws TokenAbilitiesException
     */
    public function show(string $merchant_id): MerchantResource
    {
        /** @var Merchant $merchant */
        $merchant = QueryBuilder::for(Merchant::class)
            ->findOrFail($merchant_id);

        $this->isAble('view', $merchant, 'read');

        return new MerchantResource($merchant);
    }

    /**
     * Replace merchant.
     *
     * @param  StoreMerchantRequest  $request  The request containing the updated merchant data.
     * @param  string  $merchant_id  The ID of the merchant to update.
     *
     * @return MerchantResource The updated merchant or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function replace(StoreMerchantRequest $request, string $merchant_id): MerchantResource
    {
        $merchant = Merchant::findOrFail($merchant_id);
        $merchant->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $merchant, 'update');

        $attributes = array_merge(['balance' => '0'], $request->mappedAttributes());
        $merchant->update($attributes);

        return new MerchantResource($merchant);
    }

    /**
     * Update merchant.
     *
     * @param  UpdateMerchantRequest  $request  The request containing the updated merchant data.
     * @param  string  $merchant_id  The ID of the merchant to update.
     *
     * @return MerchantResource|JsonResponse The updated merchant or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function update(UpdateMerchantRequest $request, string $merchant_id): MerchantResource|JsonResponse
    {
        $merchant = Merchant::findOrFail($merchant_id);
        $request->has('data.attributes.teamId') && $merchant->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $merchant, 'update');

        $merchant->update($request->mappedAttributes());

        return new MerchantResource($merchant);
    }

    /**
     * Delete merchant.
     *
     * @param  string  $merchant_id  The ID of the merchant to delete.
     *
     * @return JsonResponse The response indicating the result of the operation.
     *
     * @throws TokenAbilitiesException
     */
    public function destroy(string $merchant_id): JsonResponse
    {
        $merchant = Merchant::findOrFail($merchant_id);
        $this->isAble('delete', $merchant, 'delete');

        $merchant->delete();

        return response()->json([], 204);
    }
}
