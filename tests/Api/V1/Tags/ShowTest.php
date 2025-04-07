<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.tags.show', 'getJson', ['tag' => 1]);

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.tags.show', ['tag' => $tag->id]))
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'balance',
                    'color',
                    'teamId',
                    'createdAt',
                    'updatedAt',
                ],
                'links' => ['self'],
            ],
        ])
        ->assertJsonPath('data.id', $tag->id)
        ->assertJsonPath('data.attributes.name', $tag->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(
        route('api.v1.tags.show', ['tag' => $tag->id])
    )->assertStatus(403);
});

test('returns only tags that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $anotherTeam = Team::factory()->create();

    /** @var Tag $anotherTag */
    $anotherTag = Tag::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.tags.show', ['tag' => $anotherTag->id]))
        ->assertStatus(404);
});
