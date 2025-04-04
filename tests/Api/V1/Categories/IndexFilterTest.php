<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\Sanctum;

test('categories can be filtered by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredCategory = $categories->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['filter[name]' => $filteredCategory->name]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredCategory->id]);

    $otherCategories = $categories->except($filteredCategory->id)->pluck('id');
    foreach ($otherCategories as $category) {
        $response->assertJsonMissing(['id' => $category]);
    }
});

test('categories can be filtered by type', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $filteredCategory = $categories->random();
    $selectedType = $filteredCategory->type;

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['filter[type]' => $selectedType]))
        ->assertStatus(200);

    $expectedCategories = $categories->where('type', $selectedType);

    $response->assertJsonCount($expectedCategories->count(), 'data');

    foreach ($expectedCategories as $category) {
        $response->assertJsonFragment(['id' => $category->id]);
    }

    $otherCategories = $categories->where('type', '!=', $selectedType);
    foreach ($otherCategories as $category) {
        $response->assertJsonMissing(['id' => $category]);
    }
});

test('categories can be filtered by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredCategory = $categories->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['filter[balance]' => $filteredCategory->balance]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredCategory->id]);

    $otherCategories = $categories->except($filteredCategory->id)->pluck('id');
    foreach ($otherCategories as $category) {
        $response->assertJsonMissing(['id' => $category]);
    }
});

test('categories can be filtered by balance using operators', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Category> $categories */
    $categories = Category::factory(10)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $referenceBalance = $categories->sortBy('balance')->values()[2]->balance;

    // Greater than
    $expectedGt = $categories->where('balance', '>', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['filter[balance]' => '>' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGt), $response->json('data'));
    $this->assertEquals(
        collect($expectedGt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than
    $expectedLt = $categories->where('balance', '<', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['filter[balance]' => '<' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLt), $response->json('data'));
    $this->assertEquals(
        collect($expectedLt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Greater than or equal
    $expectedGte = $categories->where('balance', '>=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['filter[balance]' => '>=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGte), $response->json('data'));
    $this->assertEquals(
        collect($expectedGte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than or equal
    $expectedLte = $categories->where('balance', '<=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.categories.index', ['filter[balance]' => '<=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLte), $response->json('data'));
    $this->assertEquals(
        collect($expectedLte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );
});

test('categories can be filtered by teamId', function (): void {
    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $team1 = $user1->currentTeam;
    $team2 = $user2->currentTeam;

    $user1->teams()->attach($team2);
    $user1->refresh();

    $categoriesTeam1 = Category::factory(3)->create(['team_id' => $team1->id]);
    $categoriesTeam2 = Category::factory(2)->create(['team_id' => $team2->id]);

    Sanctum::actingAs($user1, ['read']);

    $teams = [
        $team1->id => ['visible' => $categoriesTeam1, 'hidden' => $categoriesTeam2],
        $team2->id => ['visible' => $categoriesTeam2, 'hidden' => $categoriesTeam1],
    ];

    foreach ($teams as $teamId => $testData) {
        $response = $this->actingAs($user1)
            ->getJson(route('api.v1.categories.index', ['filter[teamId]' => $teamId]))
            ->assertStatus(200)
            ->assertJsonCount(count($testData['visible']), 'data');

        foreach ($testData['visible'] as $category) {
            $response->assertJsonFragment(['id' => $category->id]);
        }

        foreach ($testData['hidden'] as $category) {
            $response->assertJsonMissing(['id' => $category->id]);
        }
    }
});
