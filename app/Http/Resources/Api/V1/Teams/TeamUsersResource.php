<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamUsersResource extends JsonResource
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

        return [
            'links' => [
                'self' => route('api.v1.teams.relationships.users', $team),
                'related' => route('api.v1.teams.users', $team),
            ],
            'data' => $team->allUsers()->map(fn (User $user) => [
                'type' => 'user',
                'id' => (string) $user->id,
            ]),
        ];
    }
}
