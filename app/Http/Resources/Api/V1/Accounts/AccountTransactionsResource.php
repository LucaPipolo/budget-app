<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Accounts;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountTransactionsResource extends JsonResource
{
    /**
     * Convert the model instance to an array.
     *
     * @param  Request  $request  The current request instance.
     *
     * @return array The array representation of the model.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function toArray(Request $request): array
    {
        /** @var Account $account */
        $account = $this->resource;

        /** @var Collection<int, Transaction> $transactions */
        $transactions = $account->transactions;

        return [
            'links' => [
                'self' => route('api.v1.accounts.relationships.transactions', $account),
                'related' => route('api.v1.accounts.transactions', $account),
            ],
            'data' => $transactions->map(fn (Transaction $transaction) => [
                'type' => 'transaction',
                'id' => (string) $transaction->id,
            ]),
        ];
    }
}
