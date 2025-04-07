<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.relationships.accounts', 'getJson', ['team' => '1']);

test('user with "read" token can see team accounts relationships', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $accounts = Account::factory()->count(2)->create([
        'team_id' => $team->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.accounts', ['team' => $team->id]))
        ->assertStatus(200)
        ->assertJsonStructure([
            'links' => [
                'self',
                'related',
            ],
            'data' => [
                '*' => [
                    'type',
                    'id',
                ],
            ],
        ]);

    $accountIds = $accounts->pluck('id')->toArray();
    sort($accountIds);

    $responseAccountIds = collect($response->json('data'))->pluck('id')->toArray();
    sort($responseAccountIds);

    $this->assertEquals($accountIds, $responseAccountIds);
    $this->assertCount(2, $response->json('data'));
});

test('denies access to team accounts relationships without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.accounts', ['team' => $team->id]))
        ->assertStatus(403);
});
