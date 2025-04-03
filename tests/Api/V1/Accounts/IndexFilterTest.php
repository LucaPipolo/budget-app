<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\Sanctum;

test('accounts can be filtered by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredAccount = $accounts->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[name]' => $filteredAccount->name]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredAccount->id]);

    $otherAccounts = $accounts->except($filteredAccount->id)->pluck('id');
    foreach ($otherAccounts as $account) {
        $response->assertJsonMissing(['id' => $account]);
    }
});

test('accounts can be filtered by type', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $filteredAccount = $accounts->random();
    $selectedType = $filteredAccount->type;

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[type]' => $selectedType]))
        ->assertStatus(200);

    $expectedAccounts = $accounts->where('type', $selectedType);

    $response->assertJsonCount($expectedAccounts->count(), 'data');

    foreach ($expectedAccounts as $account) {
        $response->assertJsonFragment(['id' => $account->id]);
    }

    $otherAccounts = $accounts->where('type', '!=', $selectedType);
    foreach ($otherAccounts as $account) {
        $response->assertJsonMissing(['id' => $account]);
    }
});

test('accounts can be filtered by origin', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $filteredAccount = $accounts->random();
    $selectedOrigin = $filteredAccount->origin;

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[origin]' => $selectedOrigin]))
        ->assertStatus(200);

    $expectedAccounts = $accounts->where('origin', $selectedOrigin);

    $response->assertJsonCount($expectedAccounts->count(), 'data');

    foreach ($expectedAccounts as $account) {
        $response->assertJsonFragment(['id' => $account->id]);
    }

    $otherAccounts = $accounts->where('origin', '!=', $selectedOrigin);
    foreach ($otherAccounts as $account) {
        $response->assertJsonMissing(['id' => $account]);
    }
});

test('accounts can be filtered by currency', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $filteredAccount = $accounts->random();
    $selectedCurrency = $filteredAccount->currency;

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[currency]' => $selectedCurrency]))
        ->assertStatus(200);

    $expectedAccounts = $accounts->where('currency', $selectedCurrency);

    $response->assertJsonCount($expectedAccounts->count(), 'data');

    foreach ($expectedAccounts as $account) {
        $response->assertJsonFragment(['id' => $account->id]);
    }

    $otherAccounts = $accounts->where('currency', '!=', $selectedCurrency);
    foreach ($otherAccounts as $account) {
        $response->assertJsonMissing(['id' => $account]);
    }
});

test('accounts can be filtered by iban', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredAccount = $accounts->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[iban]' => $filteredAccount->iban]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredAccount->id]);

    $otherAccounts = $accounts->except($filteredAccount->id)->pluck('id');
    foreach ($otherAccounts as $account) {
        $response->assertJsonMissing(['id' => $account]);
    }
});

test('accounts can be filtered by swift', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredAccount = $accounts->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[swift]' => $filteredAccount->swift]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredAccount->id]);

    $otherAccounts = $accounts->except($filteredAccount->id)->pluck('id');
    foreach ($otherAccounts as $account) {
        $response->assertJsonMissing(['id' => $account]);
    }
});

test('accounts can be filtered by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredAccount = $accounts->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[balance]' => $filteredAccount->balance]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredAccount->id]);

    $otherAccounts = $accounts->except($filteredAccount->id)->pluck('id');
    foreach ($otherAccounts as $account) {
        $response->assertJsonMissing(['id' => $account]);
    }
});

test('accounts can be filtered by balance using operators', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Account> $accounts */
    $accounts = Account::factory(10)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $referenceBalance = $accounts->sortBy('balance')->values()[2]->balance;

    // Greater than
    $expectedGt = $accounts->where('balance', '>', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[balance]' => '>' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGt), $response->json('data'));
    $this->assertEquals(
        collect($expectedGt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than
    $expectedLt = $accounts->where('balance', '<', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[balance]' => '<' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLt), $response->json('data'));
    $this->assertEquals(
        collect($expectedLt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Greater than or equal
    $expectedGte = $accounts->where('balance', '>=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[balance]' => '>=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGte), $response->json('data'));
    $this->assertEquals(
        collect($expectedGte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than or equal
    $expectedLte = $accounts->where('balance', '<=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index', ['filter[balance]' => '<=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLte), $response->json('data'));
    $this->assertEquals(
        collect($expectedLte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );
});

test('accounts can be filtered by teamId', function (): void {
    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $team1 = $user1->currentTeam;
    $team2 = $user2->currentTeam;

    $user1->teams()->attach($team2);
    $user1->refresh();

    $accountsTeam1 = Account::factory(3)->create(['team_id' => $team1->id]);
    $accountsTeam2 = Account::factory(2)->create(['team_id' => $team2->id]);

    Sanctum::actingAs($user1, ['read']);

    $teams = [
        $team1->id => ['visible' => $accountsTeam1, 'hidden' => $accountsTeam2],
        $team2->id => ['visible' => $accountsTeam2, 'hidden' => $accountsTeam1],
    ];

    foreach ($teams as $teamId => $testData) {
        $response = $this->actingAs($user1)
            ->getJson(route('api.v1.accounts.index', ['filter[teamId]' => $teamId]))
            ->assertStatus(200)
            ->assertJsonCount(count($testData['visible']), 'data');

        foreach ($testData['visible'] as $account) {
            $response->assertJsonFragment(['id' => $account->id]);
        }

        foreach ($testData['hidden'] as $account) {
            $response->assertJsonMissing(['id' => $account->id]);
        }
    }
});
