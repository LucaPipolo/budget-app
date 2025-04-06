<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.categories', 'getJson', ['team' => 1]);

test('returns team categories to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $categories = Category::factory()->count(3)->create([
        'team_id' => $team->id,
    ]);

    $expectedCategoryIds = $categories->pluck('id')->toArray();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.categories', ['team' => $team->id]))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $responseCategoryIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($expectedCategoryIds as $categoryId) {
        $this->assertContains($categoryId, $responseCategoryIds, "Category ID {$categoryId} is missing from the response");
    }
});

test('denies team categories to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.categories', ['team' => $team->id]))
        ->assertStatus(403);
});

test('returns only categories associated with the specified team', function (): void {
    $owner = User::factory()->create();

    $team1 = Team::factory()->create(['user_id' => $owner->id]);
    $team2 = Team::factory()->create(['user_id' => $owner->id]);

    $team1Accounts = Category::factory()->count(2)->create([
        'team_id' => $team1->id,
    ]);

    $team2Accounts = Category::factory()->count(2)->create([
        'team_id' => $team2->id,
    ]);

    Sanctum::actingAs($owner, ['read']);

    // Test categories for team 1
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.categories', ['team' => $team1->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseCategoryIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team1Accounts->pluck('id')->toArray() as $categoryId) {
        $this->assertContains($categoryId, $responseCategoryIds);
    }

    foreach ($team2Accounts->pluck('id')->toArray() as $categoryId) {
        $this->assertNotContains($categoryId, $responseCategoryIds);
    }

    // Test categories for team 2
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.categories', ['team' => $team2->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseCategoryIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team2Accounts->pluck('id')->toArray() as $categoryId) {
        $this->assertContains($categoryId, $responseCategoryIds);
    }

    foreach ($team1Accounts->pluck('id')->toArray() as $categoryId) {
        $this->assertNotContains($categoryId, $responseCategoryIds);
    }
});
