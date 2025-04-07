<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Teams;

use App\Models\Category;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamCategoriesResource extends JsonResource
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

        /** @var Collection<int, Category> $categories */
        $categories = $team->categories;

        return [
            'links' => [
                'self' => route('api.v1.teams.relationships.categories', $team),
                'related' => route('api.v1.teams.categories', $team),
            ],
            'data' => $categories->map(fn (Category $category) => [
                'type' => 'category',
                'id' => (string) $category->id,
            ]),
        ];
    }
}
