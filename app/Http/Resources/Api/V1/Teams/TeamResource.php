<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Teams;

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Tag;
use App\Models\Team;
use App\Models\Transaction;
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
                $this->relationLoaded('users') || $this->relationLoaded('accounts') ||
                $this->relationLoaded('merchants') || $this->relationLoaded('categories') ||
                $this->relationLoaded('tags') || $this->relationLoaded('transactions'),
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
                            'accounts',
                            function () {
                                /** @var Collection<int, Account> $accounts */
                                $accounts = $this->accounts;

                                return [
                                    'accounts' => [
                                        'data' => $accounts->map(fn (Account $account) => [
                                            'type' => 'account',
                                            'id' => (string) $account->id,
                                        ]),
                                    ],
                                ];
                            },
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
                        ),
                        // @phpstan-ignore-next-line arguments.count
                        $this->whenLoaded(
                            'categories',
                            function () {
                                /** @var Collection<int, Category> $categories */
                                $categories = $this->categories;

                                return [
                                    'categories' => [
                                        'data' => $categories->map(fn (Category $category) => [
                                            'type' => 'category',
                                            'id' => (string) $category->id,
                                        ]),
                                    ],
                                ];
                            },
                            []
                        ),
                        // @phpstan-ignore-next-line arguments.count
                        $this->whenLoaded(
                            'tags',
                            function () {
                                /** @var Collection<int, Tag> $tags */
                                $tags = $this->tags;

                                return [
                                    'tags' => [
                                        'data' => $tags->map(fn (Tag $tag) => [
                                            'type' => 'tag',
                                            'id' => (string) $tag->id,
                                        ]),
                                    ],
                                ];
                            },
                            []
                        ),
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
                'self' => route('api.v1.teams.show', ['team' => $this->id]),
            ],
        ];
    }
}
