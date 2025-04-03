<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.accounts.replace', 'putJson', ['account' => 1]);

test('user with "update" token can replace an account', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Account Name',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.accounts.replace', $account->id), $updatedData)
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
        ->assertJsonPath('data.attributes.name', 'Updated Account Name');

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'name' => 'Updated Account Name',
    ]);
});

test('denies replace to user without "update" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $originalName = $account->name;

    Sanctum::actingAs($user, ['read', 'create', 'delete']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Account Name',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.accounts.replace', $account->id), $updatedData)
        ->assertStatus(403);

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'name' => $originalName,
    ]);
});

test('user cannot replace an account that does not belong to them', function (): void {
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
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.accounts.replace', $anotherAccount->id), $updatedData)
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
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->putJson(route('api.v1.accounts.replace', $account->id), $updatedData)
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
