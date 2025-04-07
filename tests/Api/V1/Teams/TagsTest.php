<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.tags', 'getJson', ['team' => 1]);

test('returns team tags to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $tags = Tag::factory()->count(3)->create([
        'team_id' => $team->id,
    ]);

    $expectedTagIds = $tags->pluck('id')->toArray();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.tags', ['team' => $team->id]))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $responseTagIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($expectedTagIds as $tagId) {
        $this->assertContains($tagId, $responseTagIds, "Tag ID {$tagId} is missing from the response");
    }
});

test('denies team tags to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.tags', ['team' => $team->id]))
        ->assertStatus(403);
});

test('returns only tags associated with the specified team', function (): void {
    $owner = User::factory()->create();

    $team1 = Team::factory()->create(['user_id' => $owner->id]);
    $team2 = Team::factory()->create(['user_id' => $owner->id]);

    $team1Tags = Tag::factory()->count(2)->create([
        'team_id' => $team1->id,
    ]);

    $team2Tags = Tag::factory()->count(2)->create([
        'team_id' => $team2->id,
    ]);

    Sanctum::actingAs($owner, ['read']);

    // Test tags for team 1
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.tags', ['team' => $team1->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseTagIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team1Tags->pluck('id')->toArray() as $tagId) {
        $this->assertContains($tagId, $responseTagIds);
    }

    foreach ($team2Tags->pluck('id')->toArray() as $tagId) {
        $this->assertNotContains($tagId, $responseTagIds);
    }

    // Test tags for team 2
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.tags', ['team' => $team2->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseTagIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team2Tags->pluck('id')->toArray() as $tagId) {
        $this->assertContains($tagId, $responseTagIds);
    }

    foreach ($team1Tags->pluck('id')->toArray() as $tagId) {
        $this->assertNotContains($tagId, $responseTagIds);
    }
});
