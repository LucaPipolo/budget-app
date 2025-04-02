<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.merchants.store');

test('user with "create" token can create a team', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Merchant',
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
        ->assertStatus(201)
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
        ]);

    $this->assertDatabaseHas('merchants', [
        'name' => 'New Test Merchant',
        'balance' => 1333,
        'team_id' => $user->currentTeam->id,
    ]);
});

test('denies creation to user without "create" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['read', 'update', 'delete']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Merchant',
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
        ->assertStatus(403);

    $this->assertDatabaseMissing('merchants', [
        'name' => 'New Test Merchant',
        'team_id' => $user->currentTeam->id,
    ]);
});

test('user cannot create a merchant assigned to a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    Sanctum::actingAs($user, ['create']);

    $updatedData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Merchant',
                'balance' => 1333,
                'teamId' => $anotherTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $updatedData)
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
        'name' => 'New Test Merchant',
    ]);
});
