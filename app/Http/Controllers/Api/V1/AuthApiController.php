<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\InvalidRefreshTokenException;
use App\Http\Resources\Api\V1\AuthResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Sanctum\PersonalAccessToken;

class AuthApiController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @param  AuthService  $authService  The authentication service instance.
     */
    public function __construct(
        private readonly AuthService $authService
    ) {
        // Preserve brace position.
    }

    /**
     * Login.
     *
     * @param  LoginRequest  $request  The login request.
     *
     * @return AuthResource The JSON response.
     *
     * @throws InvalidCredentialsException
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): AuthResource
    {
        $request->validated($request->all());

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw new InvalidCredentialsException();
        }

        $tokens = $this->authService->generateTokens(Auth::user());

        return new AuthResource($tokens['accessToken'], $tokens['refreshToken']);
    }

    /**
     * Get logged user info.
     *
     * @return UserResource The User resource.
     */
    public function me(): UserResource
    {
        return new UserResource(Auth::user());
    }

    /**
     * Logout.
     *
     * @param  LoginRequest  $request  The login request.
     *
     * @return JsonResponse The JSON response.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return new JsonResponse('', 204);
    }

    /**
     * Refresh token.
     *
     * @return AuthResource Returns a JSON response with the new access token.
     *
     * @throws InvalidRefreshTokenException
     */
    public function refreshToken(): AuthResource
    {
        $tokens = $this->authService->validateRefreshToken(Auth::user());

        return new AuthResource($tokens['accessToken'], $tokens['refreshToken']);
    }
}
