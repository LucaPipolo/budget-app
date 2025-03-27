<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Convert the model instance to an array.
     *
     * @param  Request  $request  The current request instance.
     *
     * @return array The array representation of the model.
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'type' => 'user',
            'id' => (string) $user->id,
            'attributes' => [
                'name' => $user->name,
                'email' => $user->email,
                'profilePhotoUrl' => $user->profile_photo_url,
                'emailVerifiedAt' => $user->email_verified_at,
                'hasEnabledTwoFactorsAuth' => $user->hasEnabledTwoFactorAuthentication(),
                'createdAt' => $user->created_at,
                'updatedAt' => $user->updated_at,
            ],
            'links' => $this->when(
                $request->routeIs('api.v1.auth.me'),
                [
                    'self' => route('api.v1.auth.me'),
                ]
            ),
        ];
    }
}
