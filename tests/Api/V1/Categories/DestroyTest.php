<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

assertEndpointRequiresAuthentication('api.v1.categories.destroy', 'deleteJson', ['category' => 1]);

test('user with "delete" token can delete a category', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.categories.destroy', $category->id))
        ->assertStatus(204);

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

test('denies deletion to user without "delete" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read', 'create', 'update']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.categories.destroy', $category->id))
        ->assertStatus(403);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
    ]);
});

test('user cannot delete a category that does not belong to them', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    $anotherCategory = Category::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($user, ['delete']);

    $this->actingAs($user)
        ->deleteJson(route('api.v1.categories.destroy', $anotherCategory->id))
        ->assertStatus(404);

    $this->assertDatabaseHas('categories', [
        'id' => $anotherCategory->id,
    ]);
});
