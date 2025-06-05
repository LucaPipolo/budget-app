<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->user = User::factory()->withPersonalTeam()->create();
    $this->team = $this->user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $this->team]);
    $this->category = Category::factory()->create(['team_id' => $this->team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $this->team]);
});

test('transactions can be sorted by amount', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Transaction> $transactions */
    $transactions = Transaction::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $transactions->sortBy('amount')->pluck('amount')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => 'amount']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTransactions = collect($response->json('data'))->pluck('attributes.amount')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTransactions);

    // DESC Sort
    $expectedDesc = $transactions->sortByDesc('amount')->pluck('amount')->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => '-amount']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTransactions = collect($response->json('data'))->pluck('attributes.amount')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTransactions);
});

test('transactions can be sorted by date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Transaction> $transactions */
    $transactions = Transaction::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'date' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $transactions
        ->sortBy('date')
        ->values()
        ->map(fn ($t) => $t->date->format('Y-m-d H:i:s') . substr($t->date->format('P'), 0, 3))
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => 'date']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTransactions = collect($response->json('data'))->pluck('attributes.date')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTransactions);

    // DESC Sort
    $expectedDesc = $transactions
        ->sortByDesc('date')
        ->values()
        ->map(fn ($t) => $t->date->format('Y-m-d H:i:s') . substr($t->date->format('P'), 0, 3))
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => '-date']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTransactions = collect($response->json('data'))->pluck('attributes.date')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTransactions);
});

test('transactions can be sorted by creation date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Transaction> $transactions */
    $transactions = Transaction::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'created_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $transactions
        ->sortBy('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => 'createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTransactions = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTransactions);

    // DESC Sort
    $expectedDesc = $transactions
        ->sortByDesc('created_at')
        ->values()
        ->map(fn ($t) => $t->created_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => '-createdAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTransactions = collect($response->json('data'))->pluck('attributes.createdAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTransactions);
});

test('transactions can be sorted by update date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Transaction> $transactions */
    $transactions = Transaction::factory(3)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'updated_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    Sanctum::actingAs($user, ['read']);

    // ASC Sort
    $expectedAsc = $transactions
        ->sortBy('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => 'updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedAscTransactions = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedAsc, $sortedAscTransactions);

    // DESC Sort
    $expectedDesc = $transactions
        ->sortByDesc('updated_at')
        ->values()
        ->map(fn ($t) => $t->updated_at->toISOString())
        ->toArray();

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.transactions.index', ['sort' => '-updatedAt']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');

    $sortedDescTransactions = collect($response->json('data'))->pluck('attributes.updatedAt')->toArray();
    $this->assertEquals($expectedDesc, $sortedDescTransactions);
});
