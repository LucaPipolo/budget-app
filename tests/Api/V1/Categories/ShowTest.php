<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.categories.show', 'getJson', ['category' => 1]);

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.categories.show', ['category' => $category->id]))
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
        ->assertJsonPath('data.attributes.name', $category->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(
        route('api.v1.categories.show', ['category' => $category->id])
    )->assertStatus(403);
});

test('returns only categories that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $anotherTeam = Team::factory()->create();

    /** @var Category $anotherCategory */
    $anotherCategory = Category::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.categories.show', ['category' => $anotherCategory->id]))
        ->assertStatus(404);
});
