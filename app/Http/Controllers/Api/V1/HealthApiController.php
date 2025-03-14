<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Api\V1\HealthResource;

class HealthApiController extends ApiController
{
    /**
     * Health check.
     *
     * @return HealthResource The JSON response.
     *
     * @unauthenticated
     */
    public function check(): HealthResource
    {
        return new HealthResource(['status' => 'healthy']);
    }
}
