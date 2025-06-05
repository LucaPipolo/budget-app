<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Merchants;

use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantTransactionsResource extends JsonResource
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
        /** @var Merchant $merchant */
        $merchant = $this->resource;

        /** @var Collection<int, Transaction> $transactions */
        $transactions = $merchant->transactions;

        return [
            'links' => [
                'self' => route('api.v1.merchants.relationships.transactions', $merchant),
                'related' => route('api.v1.merchants.transactions', $merchant),
            ],
            'data' => $transactions->map(fn (Transaction $transaction) => [
                'type' => 'transaction',
                'id' => (string) $transaction->id,
            ]),
        ];
    }
}
