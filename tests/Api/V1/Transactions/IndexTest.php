<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

beforeEach(function (): void {
    $this->user = User::factory()->withPersonalTeam()->create();
    $this->team = $this->user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $this->team]);
    $this->category = Category::factory()->create(['team_id' => $this->team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $this->team]);
});

assertEndpointRequiresAuthentication('api.v1.transactions.index', 'getJson');

test('returns data to user with "read" token', function (): void {
    $transaction = Transaction::factory()->create();

    Sanctum::actingAs($this->user, ['read']);

    $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => [
                        'amount',
                        'date',
                        'notes',
                        'teamId',
                        'accountId',
                        'merchantId',
                        'categoryId',
                        'createdAt',
                        'updatedAt',
                    ],
                    'links' => ['self'],
                ],
            ],
        ])
        ->assertJsonPath('data.0.id', $transaction->id)
        ->assertJsonPath('data.0.attributes.amount', $transaction->amount);
});

test('denies data to user without "read" token', function (): void {
    Sanctum::actingAs($this->user, ['create', 'update', 'delete']);

    $this->actingAs($this->user)->getJson(route('api.v1.transactions.index'))->assertStatus(403);
});

test('returns only transactions that belong to the user', function (): void {
    $transaction = Transaction::factory()->create(['team_id' => $this->team]);

    $otherTeam = Team::factory()->create();
    $otherTransaction = Transaction::factory()->create(['team_id' => $otherTeam]);

    Sanctum::actingAs($this->user, ['read']);

    $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $transaction->id])
        ->assertJsonMissing(['id' => $otherTransaction->id]);
});
