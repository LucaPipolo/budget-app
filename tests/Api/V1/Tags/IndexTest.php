<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.tags.index', 'getJson');

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $tag = Tag::factory()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.tags.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
        ])
        ->assertJsonPath('data.0.id', $tag->id)
        ->assertJsonPath('data.0.attributes.name', $tag->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(route('api.v1.tags.index'))->assertStatus(403);
});

test('returns only tags that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $tag = Tag::factory()->for($user->currentTeam)->create();

    $otherTeam = Team::factory()->create();
    $otherTag = Tag::factory()->for($otherTeam)->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.tags.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $tag->id])
        ->assertJsonMissing(['id' => $otherTag->id]);
});
