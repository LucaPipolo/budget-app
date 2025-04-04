<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.categories.replace', 'putJson', ['category' => 1]);

test('user with "update" token can replace a category', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Category Name',
                'type' => 'income',
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.categories.replace', $category->id), $updatedData)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'type',
                    'balance',
                    'teamId',
                    'createdAt',
                    'updatedAt',
                ],
                'links' => ['self'],
            ],
        ])
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonPath('data.attributes.name', 'Updated Category Name');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category Name',
    ]);
});

test('denies replace to user without "update" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $originalName = $category->name;

    Sanctum::actingAs($user, ['read', 'create', 'delete']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Category Name',
                'type' => 'income',
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.categories.replace', $category->id), $updatedData)
        ->assertStatus(403);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => $originalName,
    ]);
});

test('user cannot replace a category that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Category $category */
    $anotherCategory = Category::factory()->create([
        'team_id' => $anotherTeam,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Hacked Category Name',
                'type' => 'income',
                'balance' => 1333,
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.categories.replace', $anotherCategory->id), $updatedData)
        ->assertStatus(404);

    $this->assertDatabaseMissing('categories', [
        'id' => $anotherCategory->id,
        'name' => 'Hacked Category Name',
    ]);
});

test('user cannot assign a category to a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Category Name',
                'type' => 'income',
                'balance' => 1333,
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.categories.replace', $category->id), $updatedData)
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

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
        'name' => 'Updated Category Name',
    ]);
});
