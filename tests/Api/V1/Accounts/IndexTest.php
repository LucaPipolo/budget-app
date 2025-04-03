<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.accounts.index', 'getJson');

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $account = Account::factory()->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
        ])
        ->assertJsonPath('data.0.id', $account->id)
        ->assertJsonPath('data.0.attributes.name', $account->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(route('api.v1.accounts.index'))->assertStatus(403);
});

test('returns only accounts that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $account = Account::factory()->for($user->currentTeam)->create();

    $otherTeam = Team::factory()->create();
    $otherAccount = Account::factory()->for($otherTeam)->create();

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.accounts.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $account->id])
        ->assertJsonMissing(['id' => $otherAccount->id]);
});
