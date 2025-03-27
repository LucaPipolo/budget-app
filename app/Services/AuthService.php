<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InvalidRefreshTokenException;
use App\Models\User;

class AuthService
{
    /**
     * Generate access and refresh tokens for the given user.
     *
     * @param  User  $user  The user instance for whom to generate the tokens.
     *
     * @return array Returns an array containing the access and refresh tokens.
     */
    public function generateTokens(User $user): array
    {
        $accessTokenExpireTime = now()->addMinutes(config('sanctum.access_token_expiration'));
        $refreshTokenExpireTime = now()->addMinutes(config('sanctum.refresh_token_expiration'));

        $user->tokens()->where('name', 'api-auto-generated-access-token')->delete();
        $accessToken = $user->createToken('api-auto-generated-access-token', ['*'], $accessTokenExpireTime);

        $user->tokens()->where('name', 'api-auto-generated-refresh-token')->delete();
        $refreshToken = $user->createToken(
            'api-auto-generated-refresh-token',
            ['issue-access-token'],
            $refreshTokenExpireTime
        );

        return [
            'accessToken' => [
                'token' => $accessToken->plainTextToken,
                'expiresIn' => config('sanctum.access_token_expiration') * 60,
                'expiresAt' => $accessTokenExpireTime,
                'expiresAtUnix' => $accessTokenExpireTime->timestamp,
            ],
            'refreshToken' => [
                'token' => $refreshToken->plainTextToken,
                'expiresIn' => config('sanctum.access_token_expiration') * 60,
                'expiresAt' => $refreshTokenExpireTime,
                'expiresAtUnix' => $refreshTokenExpireTime->timestamp,
            ],
        ];
    }

    /**
     * Validate refresh token.
     *
     * @return array Returns an array containing the access and refresh tokens.
     *
     * @throws InvalidRefreshTokenException
     */
    public function validateRefreshToken(User $user): array
    {
        $refreshToken = $user->tokens()->where('name', 'api-auto-generated-refresh-token')->first();

        if (
            ! $refreshToken ||
            ! $refreshToken->can('issue-access-token')
        ) {
            throw new InvalidRefreshTokenException();
        }

        return $this->generateTokens($user);
    }
}
