<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->user = User::factory()->withPersonalTeam()->create();
    $this->team = $this->user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $this->team]);
    $this->category = Category::factory()->create(['team_id' => $this->team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $this->team]);
});

test('transactions can be filtered by amount', function (): void {
    /** @var Collection<int, Transaction> $transactions */
    $transactions = Transaction::factory()
        ->count(3)
        ->state(new Sequence(
            fn () => [
                'team_id' => $this->user->currentTeam->id,
                'amount' => rand(-25000, 250000),
            ]
        ))
        ->create();
    $filteredTransaction = $transactions->random();

    Sanctum::actingAs($this->user, ['read']);

    $response = $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.index', ['filter[amount]' => $filteredTransaction->amount]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredTransaction->id]);

    $otherTransactions = $transactions->except($filteredTransaction->id)->pluck('id');
    foreach ($otherTransactions as $transaction) {
        $response->assertJsonMissing(['id' => $transaction]);
    }
});

test('transactions can be filtered by amount using operators', function (): void {
    /** @var Collection<int, Transaction> $transactions */
    $transactions = Transaction::factory(10)->create([
        'team_id' => $this->user->currentTeam->id,
    ]);

    Sanctum::actingAs($this->user, ['read']);

    $referenceAmount = $transactions->sortBy('amount')->values()[2]->amount;

    // Greater than
    $expectedGt = $transactions->where('amount', '>', $referenceAmount)->values()->toArray();
    $response = $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.index', ['filter[amount]' => '>' . $referenceAmount]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGt), $response->json('data'));
    $this->assertEquals(
        collect($expectedGt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than
    $expectedLt = $transactions->where('amount', '<', $referenceAmount)->values()->toArray();
    $response = $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.index', ['filter[amount]' => '<' . $referenceAmount]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLt), $response->json('data'));
    $this->assertEquals(
        collect($expectedLt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Greater than or equal
    $expectedGte = $transactions->where('amount', '>=', $referenceAmount)->values()->toArray();
    $response = $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.index', ['filter[amount]' => '>=' . $referenceAmount]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGte), $response->json('data'));
    $this->assertEquals(
        collect($expectedGte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than or equal
    $expectedLte = $transactions->where('amount', '<=', $referenceAmount)->values()->toArray();
    $response = $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.index', ['filter[amount]' => '<=' . $referenceAmount]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLte), $response->json('data'));
    $this->assertEquals(
        collect($expectedLte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );
});

test('transactions can be filtered by teamId', function (): void {
    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $team1 = $user1->currentTeam;
    $team2 = $user2->currentTeam;

    $user1->teams()->attach($team2);
    $user1->refresh();

    $transactionsTeam1 = Transaction::factory(3)->create(['team_id' => $team1->id]);
    $transactionsTeam2 = Transaction::factory(2)->create(['team_id' => $team2->id]);

    Sanctum::actingAs($user1, ['read']);

    $teams = [
        $team1->id => ['visible' => $transactionsTeam1, 'hidden' => $transactionsTeam2],
        $team2->id => ['visible' => $transactionsTeam2, 'hidden' => $transactionsTeam1],
    ];

    foreach ($teams as $teamId => $testData) {
        $response = $this->actingAs($user1)
            ->getJson(route('api.v1.transactions.index', ['filter[teamId]' => $teamId]))
            ->assertStatus(200)
            ->assertJsonCount(count($testData['visible']), 'data');

        foreach ($testData['visible'] as $transaction) {
            $response->assertJsonFragment(['id' => $transaction->id]);
        }

        foreach ($testData['hidden'] as $transaction) {
            $response->assertJsonMissing(['id' => $transaction->id]);
        }
    }
});

test('transactions can be filtered by accountId', function (): void {
    $account1 = Account::factory()->create(['team_id' => $this->team]);
    $account2 = Account::factory()->create(['team_id' => $this->team]);

    $transactionsAccount1 = Transaction::factory(3)->create(['account_id' => $account1->id]);
    $transactionsAccount2 = Transaction::factory(2)->create(['account_id' => $account2->id]);

    Sanctum::actingAs($this->user, ['read']);

    $accounts = [
        $account1->id => ['visible' => $transactionsAccount1, 'hidden' => $transactionsAccount2],
        $account2->id => ['visible' => $transactionsAccount2, 'hidden' => $transactionsAccount1],
    ];

    foreach ($accounts as $accountId => $testData) {
        $response = $this->actingAs($this->user)
            ->getJson(route('api.v1.transactions.index', ['filter[accountId]' => $accountId]))
            ->assertStatus(200)
            ->assertJsonCount(count($testData['visible']), 'data');

        foreach ($testData['visible'] as $transaction) {
            $response->assertJsonFragment(['id' => $transaction->id]);
        }

        foreach ($testData['hidden'] as $transaction) {
            $response->assertJsonMissing(['id' => $transaction->id]);
        }
    }
});

test('transactions can be filtered by merchantId', function (): void {
    $merchant1 = Merchant::factory()->create(['team_id' => $this->team]);
    $merchant2 = Merchant::factory()->create(['team_id' => $this->team]);

    $transactionMerchant1 = Transaction::factory(3)->create(['merchant_id' => $merchant1->id]);
    $transactionMerchant2 = Transaction::factory(2)->create(['merchant_id' => $merchant2->id]);

    Sanctum::actingAs($this->user, ['read']);

    $merchants = [
        $merchant1->id => ['visible' => $transactionMerchant1, 'hidden' => $transactionMerchant2],
        $merchant2->id => ['visible' => $transactionMerchant2, 'hidden' => $transactionMerchant1],
    ];

    foreach ($merchants as $merchantId => $testData) {
        $response = $this->actingAs($this->user)
            ->getJson(route('api.v1.transactions.index', ['filter[merchantId]' => $merchantId]))
            ->assertStatus(200)
            ->assertJsonCount(count($testData['visible']), 'data');

        foreach ($testData['visible'] as $transaction) {
            $response->assertJsonFragment(['id' => $transaction->id]);
        }

        foreach ($testData['hidden'] as $transaction) {
            $response->assertJsonMissing(['id' => $transaction->id]);
        }
    }
});

test('transactions can be filtered by categoryId', function (): void {
    $category1 = Category::factory()->create(['team_id' => $this->team]);
    $category2 = Category::factory()->create(['team_id' => $this->team]);

    $transactionsCategory1 = Transaction::factory(3)->create(['category_id' => $category1->id]);
    $transactionsCategory2 = Transaction::factory(2)->create(['category_id' => $category2->id]);

    Sanctum::actingAs($this->user, ['read']);

    $categories = [
        $category1->id => ['visible' => $transactionsCategory1, 'hidden' => $transactionsCategory2],
        $category2->id => ['visible' => $transactionsCategory2, 'hidden' => $transactionsCategory1],
    ];

    foreach ($categories as $categoryId => $testData) {
        $response = $this->actingAs($this->user)
            ->getJson(route('api.v1.transactions.index', ['filter[categoryId]' => $categoryId]))
            ->assertStatus(200)
            ->assertJsonCount(count($testData['visible']), 'data');

        foreach ($testData['visible'] as $transaction) {
            $response->assertJsonFragment(['id' => $transaction->id]);
        }

        foreach ($testData['hidden'] as $transaction) {
            $response->assertJsonMissing(['id' => $transaction->id]);
        }
    }
});
