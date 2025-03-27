<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;

test('teams can be filtered by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Team> $teams */
    $teams = Team::factory(3)->hasAttached($user)->create();
    $filteredTeam = $teams->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.teams.index', ['filter[name]' => $filteredTeam->name]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredTeam->id]);

    $otherTeams = $teams->except($filteredTeam->id)->pluck('id');
    foreach ($otherTeams as $team) {
        $response->assertJsonMissing(['id' => $team]);
    }
});
