<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Merchants;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MerchantResource extends JsonResource
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
        /** @var Merchant $this */
        return [ // @phpstan-ignore-line varTag.nativeType
            'type' => 'merchant',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'balance' => intval($this->balance),
                'logoUrl' => asset(Storage::url($this->logo_path)),
                'teamId' => $this->team_id,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                'self' => route('api.v1.merchants.show', ['merchant' => $this->id]),
            ],
        ];
    }
}
