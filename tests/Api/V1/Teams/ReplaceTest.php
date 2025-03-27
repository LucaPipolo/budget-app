<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.replace', 'putJson', ['team' => 1]);

test('user with "update" token can update a team', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Team Name',
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.teams.replace', $team->id), $updatedData)
        ->assertStatus(200)
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
        ])
        ->assertJsonPath('data.id', $team->id)
        ->assertJsonPath('data.attributes.name', 'Updated Team Name');

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => 'Updated Team Name',
    ]);
});

test('denies update to user without "update" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;
    $originalName = $team->name;

    Sanctum::actingAs($user, ['read', 'create', 'delete']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Team Name',
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.teams.replace', $team->id), $updatedData)
        ->assertStatus(403);

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => $originalName,
    ]);
});

test('user cannot update a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Hacked Team Name',
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.teams.replace', $anotherTeam->id), $updatedData)
        ->assertStatus(404);

    $this->assertDatabaseMissing('teams', [
        'id' => $anotherTeam->id,
        'name' => 'Hacked Team Name',
    ]);
});
