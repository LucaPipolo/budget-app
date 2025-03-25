<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\postJson;

test('user can log in with valid credentials', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $response = postJson(route('api.v1.auth.login'), [
        'email' => $user->email,
        'password' => '7Xfss!HoCiMTV',
    ]);

    $response
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
});

test('user cannot log in with invalid credentials', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $response = postJson(route('api.v1.auth.login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                [
                    'status' => '401',
                    'title' => 'Invalid Credentials',
                    'detail' => 'The credentials used to log in are not valid.',
                ],
            ],
        ]);
});
