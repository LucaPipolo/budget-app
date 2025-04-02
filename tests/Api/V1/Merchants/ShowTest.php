<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.merchants.show', 'getJson', ['merchant' => 1]);

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.merchants.show', ['merchant' => $merchant->id]))
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
        ->assertJsonPath('data.attributes.name', $merchant->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(
        route('api.v1.merchants.show', ['merchant' => $merchant->id])
    )->assertStatus(403);
});

test('returns only merchants that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $anotherTeam = Team::factory()->create();

    /** @var Merchant $merchant */
    $anotherMerchant = Merchant::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.merchants.show', ['merchant' => $anotherMerchant->id]))
        ->assertStatus(404);
});
