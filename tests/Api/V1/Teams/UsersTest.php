<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.users', 'getJson', ['team' => 1]);

test('returns team users to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $team = $user->currentTeam;

    $member1 = User::factory()->create();
    $member2 = User::factory()->create();
    $team->users()->attach([$member1->id, $member2->id]);

    $expectedUserIds = [$user->id, $member1->id, $member2->id];

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.users', ['team' => $team->id]))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $responseUserIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($expectedUserIds as $userId) {
        $this->assertContains($userId, $responseUserIds, "User ID {$userId} is missing from the response");
    }
});

test('denies team users to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.users', ['team' => $team->id]))
        ->assertStatus(403);
});

test('returns only users associated with the specified team', function (): void {
    $owner = User::factory()->create();

    $team1 = Team::factory()->create(['user_id' => $owner->id]);
    $team2 = Team::factory()->create(['user_id' => $owner->id]);

    $team1Member = User::factory()->create();
    $team1->users()->attach($team1Member);

    $team2Member = User::factory()->create();
    $team2->users()->attach($team2Member);

    Sanctum::actingAs($owner, ['read']);

    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.users', ['team' => $team1->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $this->assertTrue(collect($response->json('data'))->pluck('id')->contains($owner->id));
    $this->assertTrue(collect($response->json('data'))->pluck('id')->contains($team1Member->id));
    $this->assertFalse(collect($response->json('data'))->pluck('id')->contains($team2Member->id));

    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.users', ['team' => $team2->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $this->assertTrue(collect($response->json('data'))->pluck('id')->contains($owner->id));
    $this->assertTrue(collect($response->json('data'))->pluck('id')->contains($team2Member->id));
    $this->assertFalse(collect($response->json('data'))->pluck('id')->contains($team1Member->id));
});
