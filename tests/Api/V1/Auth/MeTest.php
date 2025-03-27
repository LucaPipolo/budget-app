<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.auth.me', 'getJson');

test('user can retrieve the logged in user info', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)->getJson(route('api.v1.auth.me'))
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'type' => 'user',
                'id' => (string) $user->id,
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'profilePhotoUrl' => $user->profile_photo_url,
                    'emailVerifiedAt' => $user->email_verified_at?->toJSON(),
                    'hasEnabledTwoFactorsAuth' => $user->hasEnabledTwoFactorAuthentication(),
                    'createdAt' => $user->created_at->toJSON(),
                    'updatedAt' => $user->updated_at?->toJSON(),
                ],
                'links' => [
                    'self' => 'http://budget-app.test/api/v1/auth/me',
                ],
            ],
        ]);
});
