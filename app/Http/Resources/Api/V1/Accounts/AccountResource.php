<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Accounts;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AccountResource extends JsonResource
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
        /** @var Account $this */
        return [ // @phpstan-ignore-line varTag.nativeType
            'type' => 'account',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'type' => $this->type,
                'origin' => $this->origin,
                'logoUrl' => asset(Storage::url($this->logo_path)),
                'balance' => intval($this->balance),
                'currency' => $this->currency,
                'iban' => $this->iban,
                'swift' => $this->swift,
                'teamId' => $this->team_id,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'relationships' => $this->when(
                $this->relationLoaded('transactions'),
                function () {
                    return array_merge(
                        // @phpstan-ignore-next-line arguments.count
                        $this->whenLoaded(
                            'transactions',
                            function () {
                                /** @var Collection<int, Transaction> $transactions */
                                $transactions = $this->transactions;

                                return [
                                    'transactions' => [
                                        'data' => $transactions->map(fn (Transaction $transaction) => [
                                            'type' => 'transaction',
                                            'id' => (string) $transaction->id,
                                        ]),
                                    ],
                                ];
                            },
                            []
                        )
                    );
                }
            ),
            'links' => [
                'self' => route('api.v1.accounts.show', ['account' => $this->id]),
            ],
        ];
    }
}
