<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.merchants', 'getJson', ['team' => 1]);

test('returns team merchants to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $merchants = Merchant::factory()->count(3)->create([
        'team_id' => $team->id,
    ]);

    $expectedMerchantIds = $merchants->pluck('id')->toArray();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.merchants', ['team' => $team->id]))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $responseMerchantIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($expectedMerchantIds as $merchantId) {
        $this->assertContains($merchantId, $responseMerchantIds, "Merchant ID {$merchantId} is missing from the response");
    }
});

test('denies team merchants to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.merchants', ['team' => $team->id]))
        ->assertStatus(403);
});

test('returns only merchants associated with the specified team', function (): void {
    $owner = User::factory()->create();

    $team1 = Team::factory()->create(['user_id' => $owner->id]);
    $team2 = Team::factory()->create(['user_id' => $owner->id]);

    $team1Merchants = Merchant::factory()->count(2)->create([
        'team_id' => $team1->id,
    ]);

    $team2Merchants = Merchant::factory()->count(2)->create([
        'team_id' => $team2->id,
    ]);

    Sanctum::actingAs($owner, ['read']);

    // Test merchants for team 1
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.merchants', ['team' => $team1->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseMerchantIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team1Merchants->pluck('id')->toArray() as $merchantId) {
        $this->assertContains($merchantId, $responseMerchantIds);
    }

    foreach ($team2Merchants->pluck('id')->toArray() as $merchantId) {
        $this->assertNotContains($merchantId, $responseMerchantIds);
    }

    // Test merchants for team 2
    $response = $this->actingAs($owner)
        ->getJson(route('api.v1.teams.merchants', ['team' => $team2->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');

    $responseMerchantIds = collect($response->json('data'))->pluck('id')->toArray();

    foreach ($team2Merchants->pluck('id')->toArray() as $merchantId) {
        $this->assertContains($merchantId, $responseMerchantIds);
    }

    foreach ($team1Merchants->pluck('id')->toArray() as $merchantId) {
        $this->assertNotContains($merchantId, $responseMerchantIds);
    }
});
