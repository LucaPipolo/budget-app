<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Handles\TokenAbilitiesException;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class ApiController extends Controller
{
    protected string $policyClass;

    /**
     * Determine if the user can perform an action on the given target model.
     *
     * @param  string  $ability  The ability to check, e.g., 'view', 'update'.
     * @param  Model|string  $targetModel  The model or class name to check the ability against.
     *
     * @return Response The result of the authorization check.
     *
     * @throws TokenAbilitiesException If token does not have correct capabilities.
     * @throws AuthorizationException If not authorized.
     */
    public function isAble(string $ability, Model|string $targetModel, string $tokenAbility): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->tokenCan($tokenAbility)) {
            throw new TokenAbilitiesException();
        }

        return $this->authorize($ability, [$targetModel, $this->policyClass]);
    }
}
