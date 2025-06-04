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

assertEndpointRequiresAuthentication('api.v1.transactions.show', 'getJson', ['transaction' => 1]);

test('returns data to user with "read" token', function (): void {
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['read']);

    $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.show', ['transaction' => $transaction->id]))
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
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
        ])
        ->assertJsonPath('data.id', $transaction->id)
        ->assertJsonPath('data.attributes.amount', $transaction->amount);
});

test('denies data to user without "read" token', function (): void {
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create', 'update', 'delete']);

    $this->actingAs($this->user)->getJson(
        route('api.v1.transactions.show', ['transaction' => $transaction->id])
    )->assertStatus(403);
});

test('returns only transactions that belong to the user', function (): void {
    $anotherTeam = Team::factory()->create();

    /** @var Transaction $anotherTransaction */
    $anotherTransaction = Transaction::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($this->user, ['read']);

    $this->actingAs($this->user)
        ->getJson(route('api.v1.transactions.show', ['transaction' => $anotherTransaction->id]))
        ->assertStatus(404);
});
