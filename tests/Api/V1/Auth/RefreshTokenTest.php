<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.auth.refresh-token');

test('user can refresh token', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $oldToken = $user->createToken('api-auto-generated-refresh-token');
    $this->assertDatabaseCount('personal_access_tokens', 1);

    $this
        ->withToken($oldToken->plainTextToken)
        ->postJson(route('api.v1.auth.refresh-token'))
        ->assertStatus(200)
        ->assertJsonStructure(
            [
                'data' => [
                    'accessToken',
                    'tokenType',
                    'expiresIn',
                    'expiresAt',
                    'expiresAtUnix',
                ],
            ]
        )
        ->assertCookie('refreshToken');

    $this->assertDatabaseMissing('personal_access_tokens', ['id' => $oldToken->accessToken->id]);
    $this->assertDatabaseCount('personal_access_tokens', 2);

    $newToken = PersonalAccessToken::first();

    $this->assertNotEquals($oldToken->accessToken->id, $newToken->id);
    $this->assertEquals($user->id, $newToken->tokenable_id);
});
