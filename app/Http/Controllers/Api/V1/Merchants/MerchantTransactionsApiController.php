<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Merchants;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Merchants\MerchantTransactionsResource;
use App\Http\Resources\Api\V1\Transactions\TransactionResource;
use App\Models\Merchant;
use App\Policies\MerchantPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MerchantTransactionsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = MerchantPolicy::class;

    /**
     * List merchants' transaction relationships.
     *
     * @return MerchantTransactionsResource The list of merchants' transaction relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function transactionsRelationships(Merchant $merchant): MerchantTransactionsResource
    {
        $this->isAble('view', $merchant, 'read');

        return new MerchantTransactionsResource($merchant);
    }

    /**
     * List merchants' transactions.
     *
     * @return AnonymousResourceCollection The list of merchants' transactions.
     *
     * @throws TokenAbilitiesException
     */
    public function transactions(Merchant $merchant): AnonymousResourceCollection
    {
        $this->isAble('view', $merchant, 'read');

        return TransactionResource::collection($merchant->transactions);
    }
}
