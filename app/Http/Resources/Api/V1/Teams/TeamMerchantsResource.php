<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Teams;

use App\Models\Merchant;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMerchantsResource extends JsonResource
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

        /** @var Collection<int, Merchant> $merchants */
        $merchants = $team->merchants;

        return [
            'links' => [
                'self' => route('api.v1.teams.relationships.merchants', $team),
                'related' => route('api.v1.teams.merchants', $team),
            ],
            'data' => $merchants->map(fn (Merchant $merchant) => [
                'type' => 'merchant',
                'id' => (string) $merchant->id,
            ]),
        ];
    }
}
