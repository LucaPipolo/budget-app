<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
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

assertEndpointRequiresAuthentication('api.v1.transactions.store');

test('user with "create" token can create a team', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $data = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $data)
        ->assertStatus(201)
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
        ]);

    $this->assertDatabaseHas('transactions', [
        'amount' => 3000,
        'notes' => 'A NEW beautiful note.',
        'team_id' => $this->team,
    ]);
});

test('denies creation to user without "create" token', function (): void {
    Sanctum::actingAs($this->user, ['read', 'update', 'delete']);

    $data = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $data)
        ->assertStatus(403);

    $this->assertDatabaseMissing('transactions', [
        'amount' => 3000,
        'notes' => 'A NEW beautiful note.',
        'team_id' => $this->team,
    ]);
});

test('user cannot create a category assigned to a team that does not belong to them', function (): void {
    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    Sanctum::actingAs($this->user, ['create']);

    $data = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $data)
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
        'notes' => 'A NEW beautiful note.',
    ]);
});
