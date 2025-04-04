<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;

test('tags can be sorted by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Tag> $tags */
    $tags = Tag::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $tags->sortBy('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => 'name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTags = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTags);

    // DESC Sort
    $expectedDesc = $tags->sortByDesc('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => '-name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTags = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTags);
});

test('tags can be sorted by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Tag> $tags */
    $tags = Tag::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $tags->sortBy('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => 'balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTags = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTags);

    // DESC Sort
    $expectedDesc = $tags->sortByDesc('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => '-balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTags = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTags);
});

test('tags can be sorted by creation date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Tag> $tags */
    $tags = Tag::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'created_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $tags
        ->sortBy('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => 'createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTags = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTags);

    // DESC Sort
    $expectedDesc = $tags
        ->sortByDesc('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => '-createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTags = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTags);
});

test('tags can be sorted by update date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Tag> $tags */
    $tags = Tag::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'updated_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $tags
        ->sortBy('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => 'updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTags = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTags);

    // DESC Sort
    $expectedDesc = $tags
        ->sortByDesc('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['sort' => '-updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTags = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTags);
});
