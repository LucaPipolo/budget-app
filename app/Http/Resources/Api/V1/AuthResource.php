<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cookie;

class AuthResource extends JsonResource
{
    protected array $accessTokenData;

    protected array $refreshTokenData;

    public function __construct(array $accessTokenData, array $refreshTokenData)
    {
        parent::__construct(null);

        $this->accessTokenData = $accessTokenData;
        $this->refreshTokenData = $refreshTokenData;
    }

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
            'accessToken' => $this->accessTokenData['token'],
            'tokenType' => 'Bearer',
            'expiresIn' => $this->accessTokenData['expiresIn'],
            'expiresAt' => $this->accessTokenData['expiresAt'],
            'expiresAtUnix' => $this->accessTokenData['expiresAtUnix'],
        ];
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request  The current request instance.
     *
     * @return JsonResponse The JSON response.
     */
    public function toResponse($request): JsonResponse // @pest-ignore-type
    {
        $response = parent::toResponse($request);

        if ($this->refreshTokenData) {
            $cookie = Cookie::make(
                'refreshToken',
                $this->refreshTokenData['token'],
                $this->refreshTokenData['expiresAt']->timestamp,
                '/auth/refresh-token',
                null,
                true,
                true,
                false,
                'strict'
            );

            $response->withCookie($cookie);
        }

        return $response;
    }
}
