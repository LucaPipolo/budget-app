<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.accounts.destroy', 'deleteJson', ['account' => 1]);

test('user with "delete" token can delete an account', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.accounts.destroy', $account->id))
        ->assertStatus(204);

    $this->assertDatabaseMissing('accounts', [
        'id' => $account->id,
    ]);
});

test('denies deletion to user without "delete" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read', 'create', 'update']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.accounts.destroy', $account->id))
        ->assertStatus(403);

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
    ]);
});

test('user cannot delete an account that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    $anotherAccount = Account::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.accounts.destroy', $anotherAccount->id))
        ->assertStatus(404);

    $this->assertDatabaseHas('accounts', [
        'id' => $anotherAccount->id,
    ]);
});
