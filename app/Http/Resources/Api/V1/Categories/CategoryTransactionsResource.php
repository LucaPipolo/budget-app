<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Categories;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTransactionsResource extends JsonResource
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
        /** @var Category $category */
        $category = $this->resource;

        /** @var Collection<int, Transaction> $transactions */
        $transactions = $category->transactions;

        return [
            'links' => [
                'self' => route('api.v1.categories.relationships.transactions', $category),
                'related' => route('api.v1.categories.transactions', $category),
            ],
            'data' => $transactions->map(fn (Transaction $transaction) => [
                'type' => 'transaction',
                'id' => (string) $transaction->id,
            ]),
        ];
    }
}
