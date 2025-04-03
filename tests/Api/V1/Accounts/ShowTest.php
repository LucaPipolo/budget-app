<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.accounts.show', 'getJson', ['account' => 1]);

test('returns data to user with "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.accounts.show', ['account' => $account->id]))
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
        ->assertJsonPath('data.attributes.name', $account->name);
});

test('denies data to user without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)->getJson(
        route('api.v1.accounts.show', ['account' => $account->id])
    )->assertStatus(403);
});

test('returns only accounts that belong to the user', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $anotherTeam = Team::factory()->create();

    /** @var Account $anotherAccount */
    $anotherAccount = Account::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $this->actingAs($user)
        ->getJson(route('api.v1.accounts.show', ['account' => $anotherAccount->id]))
        ->assertStatus(404);
});
