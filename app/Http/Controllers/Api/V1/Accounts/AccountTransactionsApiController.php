<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Accounts;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Accounts\AccountTransactionsResource;
use App\Http\Resources\Api\V1\Transactions\TransactionResource;
use App\Models\Account;
use App\Policies\AccountPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountTransactionsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = AccountPolicy::class;

    /**
     * List accounts' transaction relationships.
     *
     * @return AccountTransactionsResource The list of accounts' transaction relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function transactionsRelationships(Account $account): AccountTransactionsResource
    {
        $this->isAble('view', $account, 'read');

        return new AccountTransactionsResource($account);
    }

    /**
     * List accounts' transactions.
     *
     * @return AnonymousResourceCollection The list of accounts' transactions.
     *
     * @throws TokenAbilitiesException
     */
    public function transactions(Account $account): AnonymousResourceCollection
    {
        $this->isAble('view', $account, 'read');

        return TransactionResource::collection($account->transactions);
    }
}
