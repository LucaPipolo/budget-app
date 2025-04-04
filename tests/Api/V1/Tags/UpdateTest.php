<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.tags.update', 'patchJson', ['tag' => 1]);

test('user with "update" token can update a tag', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'balance' => 1333,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $updatedData)
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
        ->assertJsonPath('data.attributes.balance', 1333);

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'balance' => 1333,
    ]);
});

test('denies replace to user without "update" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $originalBalance = $tag->balance;

    Sanctum::actingAs($user, ['read', 'create', 'delete']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'balance' => 1333,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $updatedData)
        ->assertStatus(403);

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'balance' => $originalBalance,
    ]);
});

test('user cannot update a tag that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Tag $tag */
    $anotherTag = Tag::factory()->create([
        'team_id' => $anotherTeam,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Hacked Tag Name',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $anotherTag->id), $updatedData)
        ->assertStatus(404);

    $this->assertDatabaseMissing('tags', [
        'id' => $anotherTag->id,
        'name' => 'Hacked Tag Name',
    ]);
});

test('user cannot assign a tag to a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Tag Name',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $updatedData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Invalid Relationship',
                    'detail' => 'You are trying to create a relationship with a resource that does not exist.',
                ],
            ],
        ]);

    $this->assertDatabaseMissing('tags', [
        'id' => $tag->id,
        'name' => 'Updated Tag Name',
    ]);
});
