<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.accounts.store');

test('user with "create" token can create a team', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
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
        ->postJson(route('api.v1.accounts.store'), $accountData)
        ->assertStatus(201)
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
        ]);

    $this->assertDatabaseHas('accounts', [
        'name' => 'New Test Account',
        'balance' => 33,
        'team_id' => $user->currentTeam->id,
    ]);
});

test('denies creation to user without "create" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['read', 'update', 'delete']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
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
        ->postJson(route('api.v1.accounts.store'), $accountData)
        ->assertStatus(403);

    $this->assertDatabaseMissing('accounts', [
        'name' => 'New Test Account',
        'team_id' => $user->currentTeam->id,
    ]);
});

test('user cannot create an account assigned to a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
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
        ->postJson(route('api.v1.accounts.store'), $accountData)
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
        'name' => 'New Test Account',
    ]);
});
