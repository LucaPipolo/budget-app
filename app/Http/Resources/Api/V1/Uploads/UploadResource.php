<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Uploads;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UploadResource extends JsonResource
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
        $path = $this->resource;

        return [
            'type' => 'upload',
            'id' => pathinfo($path, PATHINFO_FILENAME),
            'attributes' => [
                'path' => $path,
                'url' => asset(Storage::url($path)),
            ],
        ];
    }
}
