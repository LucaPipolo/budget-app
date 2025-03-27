<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.show', 'getJson', ['team' => 1]);

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.show', ['team' => $user->currentTeam->id]))
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
        ->assertJsonPath('data.id', $user->currentTeam->id)
        ->assertJsonPath('data.attributes.name', $user->currentTeam->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(
        route('api.v1.teams.show', ['team' => $user->currentTeam->id])
    )->assertStatus(403);
});

test('returns only teams that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $anotherTeam = Team::factory()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.show', ['team' => $anotherTeam->id]))
        ->assertStatus(404);
});
