<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Transactions;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
        /** @var Transaction $this */
        return [ // @phpstan-ignore-line varTag.nativeType
            'type' => 'transaction',
            'id' => $this->id,
            'attributes' => [
                'amount' => intval($this->amount),
                'date' => $this->date,
                'notes' => $this->notes,
                'accountId' => $this->account_id,
                'merchantId' => $this->merchant_id,
                'categoryId' => $this->category_id,
                'teamId' => $this->team_id,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                'self' => route('api.v1.transactions.show', ['transaction' => $this->id]),
            ],
        ];
    }
}
