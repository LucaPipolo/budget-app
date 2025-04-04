<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.categories.index', 'getJson');

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $category = Category::factory()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.categories.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
        ])
        ->assertJsonPath('data.0.id', $category->id)
        ->assertJsonPath('data.0.attributes.name', $category->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(route('api.v1.categories.index'))->assertStatus(403);
});

test('returns only categories that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $category = Category::factory()->for($user->currentTeam)->create();

    $otherTeam = Team::factory()->create();
    $otherCategory = Category::factory()->for($otherTeam)->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.categories.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $category->id])
        ->assertJsonMissing(['id' => $otherCategory->id]);
});
