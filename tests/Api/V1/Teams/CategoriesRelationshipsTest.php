<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.relationships.categories', 'getJson', ['team' => '1']);

test('user with "read" token can see team categories relationships', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $categories = Category::factory()->count(2)->create([
        'team_id' => $team->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.categories', ['team' => $team->id]))
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

    $categoryIds = $categories->pluck('id')->toArray();
    sort($categoryIds);

    $responseCategoryIds = collect($response->json('data'))->pluck('id')->toArray();
    sort($responseCategoryIds);

    $this->assertEquals($categoryIds, $responseCategoryIds);
    $this->assertCount(2, $response->json('data'));
});

test('denies access to team categories relationships without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.categories', ['team' => $team->id]))
        ->assertStatus(403);
});
