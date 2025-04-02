<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.merchants.index', 'getJson');

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $merchant = Merchant::factory()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
        ])
        ->assertJsonPath('data.0.id', $merchant->id)
        ->assertJsonPath('data.0.attributes.name', $merchant->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(route('api.v1.merchants.index'))->assertStatus(403);
});

test('returns only merchants that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $merchant = Merchant::factory()->for($user->currentTeam)->create();

    $otherTeam = Team::factory()->create();
    $otherMerchant = Merchant::factory()->for($otherTeam)->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $merchant->id])
        ->assertJsonMissing(['id' => $otherMerchant->id]);
});
