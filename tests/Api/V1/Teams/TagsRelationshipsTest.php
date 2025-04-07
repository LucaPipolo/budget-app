<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.relationships.tags', 'getJson', ['team' => '1']);

test('user with "read" token can see team tags relationships', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $tags = Tag::factory()->count(2)->create([
        'team_id' => $team->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.tags', ['team' => $team->id]))
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

    $tagIds = $tags->pluck('id')->toArray();
    sort($tagIds);

    $responseTagIds = collect($response->json('data'))->pluck('id')->toArray();
    sort($responseTagIds);

    $this->assertEquals($tagIds, $responseTagIds);
    $this->assertCount(2, $response->json('data'));
});

test('denies access to team tags relationships without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.tags', ['team' => $team->id]))
        ->assertStatus(403);
});
