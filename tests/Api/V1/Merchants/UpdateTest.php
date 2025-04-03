<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.merchants.update', 'patchJson', ['merchant' => 1]);

test('user with "update" token can update a merchant', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'balance' => 33,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'balance',
                    'logoUrl',
                    'teamId',
                    'createdAt',
                    'updatedAt',
                ],
                'links' => ['self'],
            ],
        ])
        ->assertJsonPath('data.id', $merchant->id)
        ->assertJsonPath('data.attributes.balance', 33);

    $this->assertDatabaseHas('merchants', [
        'id' => $merchant->id,
        'balance' => 33,
    ]);
});

test('denies replace to user without "update" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $originalBalance = $merchant->balance;

    Sanctum::actingAs($user, ['read', 'create', 'delete']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'balance' => 33,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
        ->assertStatus(403);

    $this->assertDatabaseHas('merchants', [
        'id' => $merchant->id,
        'balance' => $originalBalance,
    ]);
});

test('user cannot update a merchant that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Merchant $anotherMerchant */
    $anotherMerchant = Merchant::factory()->create([
        'team_id' => $anotherTeam,
    ]);

    Sanctum::actingAs($user, ['update']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'Hacked Merchant Name',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $anotherMerchant->id), $merchantData)
        ->assertStatus(404);

    $this->assertDatabaseMissing('merchants', [
        'id' => $anotherMerchant->id,
        'name' => 'Hacked Merchant Name',
    ]);
});

test('user cannot assign a merchant to a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['update']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'Updated Merchant Name',
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

    $this->assertDatabaseMissing('merchants', [
        'id' => $merchant->id,
        'name' => 'Updated Merchant Name',
    ]);
});
