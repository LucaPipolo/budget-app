<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
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

assertEndpointRequiresAuthentication('api.v1.transactions.replace', 'putJson', ['transaction' => 1]);

test('user with "update" token can replace a transaction', function (): void {
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->putJson(route('api.v1.transactions.replace', $transaction->id), $updatedData)
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
        ->assertJsonPath('data.attributes.notes', 'A beautiful note.');

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'notes' => 'A beautiful note.',
    ]);
});

test('denies replace to user without "update" token', function (): void {
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);
    $originalAmount = $transaction->amount;

    Sanctum::actingAs($this->user, ['read', 'create', 'delete']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->putJson(route('api.v1.transactions.replace', $transaction->id), $updatedData)
        ->assertStatus(403);

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'amount' => $originalAmount,
    ]);
});

test('user cannot replace a transaction that does not belong to them', function (): void {
    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Transaction $transaction */
    $anotherTransaction = Transaction::factory()->create([
        'team_id' => $anotherTeam,
    ]);

    Sanctum::actingAs($this->user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->putJson(route('api.v1.transactions.replace', $anotherTransaction->id), $updatedData)
        ->assertStatus(404);

    $this->assertDatabaseMissing('transactions', [
        'id' => $anotherTransaction->id,
        'amount' => 3000,
    ]);
});

test('user cannot assign a transaction to a team that does not belong to them', function (): void {
    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->putJson(route('api.v1.transactions.replace', $transaction->id), $updatedData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Invalid Relationship',
                    'detail' => 'You are trying to create a relationship with a resource that does not exist.',
                ],
            ],
        ]);

    $this->assertDatabaseMissing('transactions', [
        'id' => $transaction->id,
        'amount' => 3000,
    ]);
});
