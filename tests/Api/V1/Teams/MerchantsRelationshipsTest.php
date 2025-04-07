<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.relationships.merchants', 'getJson', ['team' => '1']);

test('user with "read" token can see team merchants relationships', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $merchants = Merchant::factory()->count(2)->create([
        'team_id' => $team->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.merchants', ['team' => $team->id]))
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

    $merchantIds = $merchants->pluck('id')->toArray();
    sort($merchantIds);

    $responseMerchantIds = collect($response->json('data'))->pluck('id')->toArray();
    sort($responseMerchantIds);

    $this->assertEquals($merchantIds, $responseMerchantIds);
    $this->assertCount(2, $response->json('data'));
});

test('denies access to team merchants relationships without "read" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['create', 'update', 'delete']);

    $this->actingAs($user)
        ->getJson(route('api.v1.teams.relationships.merchants', ['team' => $team->id]))
        ->assertStatus(403);
});
