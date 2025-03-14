<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthResource extends JsonResource
{
    /**
     * Disable wrapping for this resource.
     *
     * @var string|null
     */
    public static $wrap = null;

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
        return [
            'status' => 'healty',
        ];
    }
}
