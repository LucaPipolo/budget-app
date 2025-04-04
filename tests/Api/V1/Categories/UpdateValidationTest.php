<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('validates category name is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'name' => 123,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.name field must be a string.',
                ],
            ],
        ]);
});

test('validates category name minimum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.name field must be at least 3 characters.',
                ],
            ],
        ]);
});

test('validates category name maximum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.name field must not be greater than 255 characters.',
                ],
            ],
        ]);
});

test('validates category balance is an integer', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'balance' => 1333.5,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.balance field must be an integer.',
                ],
            ],
        ]);
});

test('validates category balance minimum value', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'balance' => -1,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.balance field must be at least 0.',
                ],
            ],
        ]);
});

test('validates category team id format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.team id field must be a valid UUID.',
                ],
            ],
        ]);
});

test('validates category team id exists', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The selected data.attributes.team id is invalid.',
                ],
            ],
        ]);
});

test('validates category type is between accepted', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Category $category */
    $category = Category::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $categoryData = [
        'data' => [
            'attributes' => [
                'type' => 'invalid-type',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.categories.update', $category->id), $categoryData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The selected data.attributes.type is invalid.',
                ],
            ],
        ]);
});
