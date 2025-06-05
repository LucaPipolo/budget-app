<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Categories;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
        /** @var Category $this */
        return [ // @phpstan-ignore-line varTag.nativeType
            'type' => 'category',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'type' => $this->type,
                'balance' => intval($this->balance),
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
                'self' => route('api.v1.categories.show', ['category' => $this->id]),
            ],
        ];
    }
}
