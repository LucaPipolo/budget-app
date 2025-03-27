<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.store');

test('user with "create" token can create a team', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['create']);

    $teamData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Team',
            ],
        ],
    ];

    $response = $this->actingAs($user)
        ->postJson(route('api.v1.teams.store'), $teamData)
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'createdAt',
                    'updatedAt',
                ],
                'links' => ['self'],
            ],
        ]);

    $this->assertDatabaseHas('teams', [
        'name' => 'New Test Team',
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('team_user', [
        'team_id' => $response->json('data.id'),
        'user_id' => $user->id,
    ]);
});

test('denies creation to user without "create" token', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['read', 'update', 'delete']);

    $teamData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Team',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.teams.store'), $teamData)
        ->assertStatus(403);

    $this->assertDatabaseMissing('teams', [
        'name' => 'New Test Team',
        'user_id' => $user->id,
    ]);
});
