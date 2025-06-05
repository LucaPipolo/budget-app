<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Teams;

use App\Models\Team;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamTransactionsResource extends JsonResource
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
        /** @var Team $team */
        $team = $this->resource;

        /** @var Collection<int, Transaction> $transactions */
        $transactions = $team->transactions;

        return [
            'links' => [
                'self' => route('api.v1.teams.relationships.transactions', $team),
                'related' => route('api.v1.teams.transactions', $team),
            ],
            'data' => $transactions->map(fn (Transaction $transaction) => [
                'type' => 'transaction',
                'id' => (string) $transaction->id,
            ]),
        ];
    }
}
