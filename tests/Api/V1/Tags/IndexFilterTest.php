<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\Sanctum;

test('tags can be filtered by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Tag> $tags */
    $tags = Tag::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredTag = $tags->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['filter[name]' => $filteredTag->name]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredTag->id]);

    $otherTags = $tags->except($filteredTag->id)->pluck('id');
    foreach ($otherTags as $tag) {
        $response->assertJsonMissing(['id' => $tag]);
    }
});

test('tags can be filtered by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Tag> $tags */
    $tags = Tag::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredTag = $tags->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['filter[balance]' => $filteredTag->balance]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredTag->id]);

    $otherTags = $tags->except($filteredTag->id)->pluck('id');
    foreach ($otherTags as $tag) {
        $response->assertJsonMissing(['id' => $tag]);
    }
});

test('tags can be filtered by balance using operators', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Tag> $tags */
    $tags = Tag::factory(10)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $referenceBalance = $tags->sortBy('balance')->values()[2]->balance;

    // Greater than
    $expectedGt = $tags->where('balance', '>', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['filter[balance]' => '>' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGt), $response->json('data'));
    $this->assertEquals(
        collect($expectedGt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than
    $expectedLt = $tags->where('balance', '<', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['filter[balance]' => '<' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLt), $response->json('data'));
    $this->assertEquals(
        collect($expectedLt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Greater than or equal
    $expectedGte = $tags->where('balance', '>=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['filter[balance]' => '>=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGte), $response->json('data'));
    $this->assertEquals(
        collect($expectedGte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than or equal
    $expectedLte = $tags->where('balance', '<=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.tags.index', ['filter[balance]' => '<=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLte), $response->json('data'));
    $this->assertEquals(
        collect($expectedLte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );
});

test('tags can be filtered by teamId', function (): void {
    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $team1 = $user1->currentTeam;
    $team2 = $user2->currentTeam;

    $user1->teams()->attach($team2);
    $user1->refresh();

    $tagsTeam1 = Tag::factory(3)->create(['team_id' => $team1->id]);
    $tagsTeam2 = Tag::factory(2)->create(['team_id' => $team2->id]);

    Sanctum::actingAs($user1, ['read']);

    $teams = [
        $team1->id => ['visible' => $tagsTeam1, 'hidden' => $tagsTeam2],
        $team2->id => ['visible' => $tagsTeam2, 'hidden' => $tagsTeam1],
    ];

    foreach ($teams as $teamId => $testData) {
        $response = $this->actingAs($user1)
            ->getJson(route('api.v1.tags.index', ['filter[teamId]' => $teamId]))
            ->assertStatus(200)
            ->assertJsonCount(count($testData['visible']), 'data');

        foreach ($testData['visible'] as $tag) {
            $response->assertJsonFragment(['id' => $tag->id]);
        }

        foreach ($testData['hidden'] as $tag) {
            $response->assertJsonMissing(['id' => $tag->id]);
        }
    }
});
