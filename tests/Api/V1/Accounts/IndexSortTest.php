<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;

test('accounts can be sorted by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $accounts->sortBy('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => 'name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscAccounts = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscAccounts);

    // DESC Sort
    $expectedDesc = $accounts->sortByDesc('name')->pluck('name')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => '-name']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescAccounts = collect($response->json('data'))->pluck('attributes.name')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescAccounts);
});

test('accounts can be sorted by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $accounts->sortBy('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => 'balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscAccounts = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscAccounts);

    // DESC Sort
    $expectedDesc = $accounts->sortByDesc('balance')->pluck('balance')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => '-balance']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescAccounts = collect($response->json('data'))->pluck('attributes.balance')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescAccounts);
});

test('accounts can be sorted by creation date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'created_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $accounts
        ->sortBy('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => 'createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscAccounts = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscAccounts);

    // DESC Sort
    $expectedDesc = $accounts
        ->sortByDesc('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => '-createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescAccounts = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescAccounts);
});

test('accounts can be sorted by update date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'updated_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $accounts
        ->sortBy('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => 'updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscAccounts = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscAccounts);

    // DESC Sort
    $expectedDesc = $accounts
        ->sortByDesc('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['sort' => '-updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescAccounts = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescAccounts);
});
