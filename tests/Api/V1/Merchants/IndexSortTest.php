<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('merchants can be sorted by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $merchants = Merchant::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $merchants->sortBy('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => 'name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscMerchants = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscMerchants);

    // DESC Sort
    $expectedDesc = $merchants->sortByDesc('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => '-name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescMerchants = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescMerchants);
});

test('merchants can be sorted by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $merchants = Merchant::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $merchants->sortBy('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => 'balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscMerchants = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscMerchants);

    // DESC Sort
    $expectedDesc = $merchants->sortByDesc('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => '-balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescMerchants = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescMerchants);
});

test('merchants can be sorted by creation date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $merchants = Merchant::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'created_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $merchants
        ->sortBy('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => 'createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscMerchants = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscMerchants);

    // DESC Sort
    $expectedDesc = $merchants
        ->sortByDesc('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => '-createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescMerchants = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescMerchants);
});

test('merchants can be sorted by update date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $merchants = Merchant::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'updated_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $merchants
        ->sortBy('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => 'updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscMerchants = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscMerchants);

    // DESC Sort
    $expectedDesc = $merchants
        ->sortByDesc('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['sort' => '-updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescMerchants = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescMerchants);
});
