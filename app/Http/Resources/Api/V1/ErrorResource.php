<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Disable wrapping for this resource.
     *
     * @var string|null
     */
    public static $wrap = null;

    private int $statusCode;

    /**
     * Constructor to accept both resource data and HTTP status code.
     *
     * @param  mixed  $resource
     */
    public function __construct($resource, int $statusCode)
    {
        parent::__construct($resource);
        $this->statusCode = $statusCode;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $formattedErrors = [];

        foreach ($this->resource['errors'] as $error) {
            $formattedError = [
                'status' => $error['status'] ?? (string) $this->statusCode,
                'title' => $error['title'] ?? 'Error',
            ];

            if (! empty($error['detail'])) {
                $formattedError['detail'] = $error['detail'];
            }

            $formattedErrors[] = $formattedError;
        }

        return [
            'errors' => $formattedErrors,
        ];
    }

    /**
     * Customize the response with the correct HTTP status code.
     */
    public function withResponse(Request $request, $response): JsonResponse
    {
        return $response->setStatusCode($this->statusCode);
    }
}
