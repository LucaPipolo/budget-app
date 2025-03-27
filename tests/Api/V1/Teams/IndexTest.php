<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.index', 'getJson');

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'createdAt',
                        'updatedAt',
                    ],
                    'links' => ['self'],
                ],
            ],
        ])
        ->assertJsonPath('data.0.id', $user->currentTeam->id)
        ->assertJsonPath('data.0.attributes.name', $user->currentTeam->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(route('api.v1.teams.index'))->assertStatus(403);
});

test('returns only teams that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $team1 = $user->currentTeam;
    Team::factory()->create();
    Team::factory()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $team1->id]);
});
