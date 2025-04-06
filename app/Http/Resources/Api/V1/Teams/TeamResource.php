<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Teams;

use App\Models\Merchant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
        /** @var Team $this */
        return [ // @phpstan-ignore-line varTag.nativeType
            'type' => 'team',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'relationships' => $this->when(
                $this->relationLoaded('users') || $this->relationLoaded('merchants'),
                function () {
                    return array_merge(
                        // @phpstan-ignore-next-line arguments.count
                        $this->whenLoaded(
                            'users',
                            fn () => [
                                'users' => [
                                    'data' => $this->allUsers()->map(fn (User $user) => [
                                        'type' => 'user',
                                        'id' => (string) $user->id,
                                    ]),
                                ],
                            ],
                            []
                        ),
                        // @phpstan-ignore-next-line arguments.count
                        $this->whenLoaded(
                            'merchants',
                            function () {
                                /** @var Collection<int, Merchant> $merchants */
                                $merchants = $this->merchants;

                                return [
                                    'merchants' => [
                                        'data' => $merchants->map(fn (Merchant $merchant) => [
                                            'type' => 'merchant',
                                            'id' => (string) $merchant->id,
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
                'self' => route('api.v1.teams.show', ['team' => $this->id]),
            ],
        ];
    }
}
