<?php

declare(strict_types=1);

use App\Models\User;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.auth.logout');

test('user can log out', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $token = $user->createToken('api-auto-generated-refresh-token');

    $this
        ->withToken($token->plainTextToken)
        ->postJson(route('api.v1.auth.logout'))
        ->assertStatus(204);
});
