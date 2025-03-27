<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.relationships.users', 'getJson', ['team' => '1']);

test('user with "read" token can see team users relationships', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $teamMembers = User::factory()->count(2)->create();
    $team->users()->attach($teamMembers->pluck('id'));

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.users', ['team' => $team->id]))
        ->assertStatus(200)
        ->assertJsonStructure([
            'links' => [
                'self',
                'related',
            ],
            'data' => [
                '*' => [
                    'type',
                    'id',
                ],
            ],
        ]);

    $allUserIds = [$user->id, ...$teamMembers->pluck('id')->toArray()];
    sort($allUserIds);

    $responseUserIds = collect($response->json('data'))->pluck('id')->toArray();
    sort($responseUserIds);

    $this->assertEquals($allUserIds, $responseUserIds);
    $this->assertCount(3, $response->json('data'));
});

test('denies access to team users relationships without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.users', ['team' => $team->id]))
        ->assertStatus(403);
});
