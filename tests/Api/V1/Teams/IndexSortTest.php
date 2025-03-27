<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('teams can be sorted by name', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory(3)->hasAttached($user)->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $teams->sortBy('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['sort' => 'name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTeams = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTeams);

    // DESC Sort
    $expectedDesc = $teams->sortByDesc('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['sort' => '-name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTeams = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTeams);
});

test('teams can be sorted by creation date', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory(3)
        ->hasAttached($user)
        ->sequence(fn ($sequence) => ['created_at' => now()->subDays($sequence->index)])
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $teams
        ->sortBy('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['sort' => 'createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTeams = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTeams);

    // DESC Sort
    $expectedDesc = $teams
        ->sortByDesc('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['sort' => '-createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTeams = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTeams);
});

test('teams can be sorted by update date', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory(3)
        ->hasAttached($user)
        ->sequence(fn ($sequence) => ['updated_at' => now()->subDays($sequence->index)])
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $teams
        ->sortBy('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['sort' => 'updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTeams = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTeams);

    // DESC Sort
    $expectedDesc = $teams
        ->sortByDesc('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['sort' => '-updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTeams = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTeams);
});
