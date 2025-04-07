<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Tags;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
        /** @var Tag $this */
        return [ // @phpstan-ignore-line varTag.nativeType
            'type' => 'tag',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'balance' => intval($this->balance),
                'color' => $this->color,
                'teamId' => $this->team_id,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                'self' => route('api.v1.tags.show', ['tag' => $this->id]),
            ],
        ];
    }
}
