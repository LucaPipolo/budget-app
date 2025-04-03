<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.accounts.update', 'patchJson', ['account' => 1]);

test('user with "update" token can update an account', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'balance' => 33,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $updatedData)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'type',
                    'origin',
                    'logoUrl',
                    'balance',
                    'currency',
                    'iban',
                    'swift',
                    'teamId',
                    'createdAt',
                    'updatedAt',
                ],
                'links' => ['self'],
            ],
        ])
        ->assertJsonPath('data.id', $account->id)
        ->assertJsonPath('data.attributes.balance', 33);

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'balance' => 33,
    ]);
});

test('denies replace to user without "update" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $originalBalance = $account->balance;

    Sanctum::actingAs($user, ['read', 'create', 'delete']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'balance' => 33,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $updatedData)
        ->assertStatus(403);

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'balance' => $originalBalance,
    ]);
});

test('user cannot update an account that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Account $account */
    $anotherAccount = Account::factory()->create([
        'team_id' => $anotherTeam,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Hacked Account Name',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $anotherAccount->id), $updatedData)
        ->assertStatus(404);

    $this->assertDatabaseMissing('accounts', [
        'id' => $anotherAccount->id,
        'name' => 'Hacked Account Name',
    ]);
});

test('user cannot assign an account to a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Account Name',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $updatedData)
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

    $this->assertDatabaseMissing('accounts', [
        'id' => $account->id,
        'name' => 'Updated Account Name',
    ]);
});
