<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.teams.destroy', 'deleteJson', ['team' => 1]);

test('user with "delete" token can delete a team', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.teams.destroy', $team->id))
        ->assertStatus(204);

    $this->assertDatabaseMissing('teams', [
        'id' => $team->id,
    ]);
});

test('denies deletion to user without "delete" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    Sanctum::actingAs($user, ['read', 'create', 'update']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.teams.destroy', $team->id))
        ->assertStatus(403);

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
    ]);
});

test('user cannot delete a team that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.teams.destroy', $anotherTeam->id))
        ->assertStatus(404);

    $this->assertDatabaseHas('teams', [
        'id' => $anotherTeam->id,
    ]);
});
