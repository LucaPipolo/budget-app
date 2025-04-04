<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;

test('categories can be sorted by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $categories->sortBy('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => 'name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscCategories = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscCategories);

    // DESC Sort
    $expectedDesc = $categories->sortByDesc('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => '-name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescCategories = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescCategories);
});

test('categories can be sorted by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $categories->sortBy('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => 'balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscCategories = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscCategories);

    // DESC Sort
    $expectedDesc = $categories->sortByDesc('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => '-balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescCategories = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescCategories);
});

test('categories can be sorted by creation date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'created_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $categories
        ->sortBy('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => 'createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscCategories = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscCategories);

    // DESC Sort
    $expectedDesc = $categories
        ->sortByDesc('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => '-createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescCategories = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescCategories);
});

test('categories can be sorted by update date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'updated_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $categories
        ->sortBy('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => 'updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscCategories = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscCategories);

    // DESC Sort
    $expectedDesc = $categories
        ->sortByDesc('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['sort' => '-updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescCategories = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescCategories);
});
