<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.accounts', 'getJson', ['team' => 1]);

test('returns team accounts to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $accounts = Account::factory()->count(3)->create([
        'team_id' => $team->id,
    ]);

    $expectedAccountIds = $accounts->pluck('id')->toArray();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.accounts', ['team' => $team->id]))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $responseAccountIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($expectedAccountIds as $accountId) {
        $this->assertContains($accountId, $responseAccountIds, "Account ID {$accountId} is missing from the response");
    }
});

test('denies team accounts to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.accounts', ['team' => $team->id]))
        ->assertStatus(403);
});

test('returns only accounts associated with the specified team', function (): void {
    $owner = User::factory()->create();

    $team1 = Team::factory()->create(['user_id' => $owner->id]);
    $team2 = Team::factory()->create(['user_id' => $owner->id]);

    $team1Accounts = Account::factory()->count(2)->create([
        'team_id' => $team1->id,
    ]);

    $team2Accounts = Account::factory()->count(2)->create([
        'team_id' => $team2->id,
    ]);

    Sanctum::actingAs($owner, ['read']);

    // Test accounts for team 1
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.accounts', ['team' => $team1->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseAccountIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team1Accounts->pluck('id')->toArray() as $accountId) {
        $this->assertContains($accountId, $responseAccountIds);
    }

    foreach ($team2Accounts->pluck('id')->toArray() as $accountId) {
        $this->assertNotContains($accountId, $responseAccountIds);
    }

    // Test accounts for team 2
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.accounts', ['team' => $team2->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseAccountIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team2Accounts->pluck('id')->toArray() as $accountId) {
        $this->assertContains($accountId, $responseAccountIds);
    }

    foreach ($team1Accounts->pluck('id')->toArray() as $accountId) {
        $this->assertNotContains($accountId, $responseAccountIds);
    }
});
