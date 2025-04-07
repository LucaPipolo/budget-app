<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.tags.destroy', 'deleteJson', ['tag' => 1]);

test('user with "delete" token can delete a tag', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.tags.destroy', $tag->id))
        ->assertStatus(204);

    $this->assertDatabaseMissing('tags', [
        'id' => $tag->id,
    ]);
});

test('denies deletion to user without "delete" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read', 'create', 'update']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.tags.destroy', $tag->id))
        ->assertStatus(403);

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
    ]);
});

test('user cannot delete a tag that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    $anotherTag = Tag::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.tags.destroy', $anotherTag->id))
        ->assertStatus(404);

    $this->assertDatabaseHas('tags', [
        'id' => $anotherTag->id,
    ]);
});
