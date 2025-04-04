<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.categories.update', 'patchJson', ['category' => 1]);

test('user with "update" token can update a category', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
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
        ->patchJson(route('api.v1.categories.update', $category->id), $updatedData)
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
        ->assertJsonPath('data.attributes.balance', 1333);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'balance' => 1333,
    ]);
});

test('denies replace to user without "update" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $originalBalance = $category->balance;

    Sanctum::actingAs($user, ['read', 'create', 'delete']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'balance' => 1333,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $updatedData)
        ->assertStatus(403);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'balance' => $originalBalance,
    ]);
});

test('user cannot update a category that does not belong to them', function (): void {
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
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $anotherCategory->id), $updatedData)
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
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $updatedData)
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
