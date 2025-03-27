<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('includes users when parameter is provided', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['include' => 'users']))
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'relationships' => [
                        'users' => [
                            'data' => [
                                '*' => [
                                    'type',
                                    'id',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                '*' => [
                    'type',
                    'id',
                    'attributes',
                ],
            ],
        ])
        ->assertJsonPath('data.0.id', $user->currentTeam->id)
        ->assertJsonPath('data.0.relationships.users.data.0.type', 'user')
        ->assertJsonPath('included.0.type', 'user')
        ->assertJsonPath('included.0.id', $user->id)
        ->assertJsonPath('included.0.attributes.email', $user->email);
});
