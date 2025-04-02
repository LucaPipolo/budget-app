<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.merchants.destroy', 'deleteJson', ['merchant' => 1]);

test('user with "delete" token can delete a merchant', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.merchants.destroy', $merchant->id))
        ->assertStatus(204);

    $this->assertDatabaseMissing('merchants', [
        'id' => $merchant->id,
    ]);
});

test('denies deletion to user without "delete" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read', 'create', 'update']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.merchants.destroy', $merchant->id))
        ->assertStatus(403);

    $this->assertDatabaseHas('merchants', [
        'id' => $merchant->id,
    ]);
});

test('user cannot delete a merchant that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    $anotherMerchant = Merchant::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.merchants.destroy', $anotherMerchant->id))
        ->assertStatus(404);

    $this->assertDatabaseHas('merchants', [
        'id' => $anotherMerchant->id,
    ]);
});
