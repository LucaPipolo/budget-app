<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('validates tag name is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 123,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
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

test('validates tag name minimum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
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

test('validates tag name maximum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
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

test('validates tag balance is an integer', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'balance' => 1333.5,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
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

test('validates tag balance minimum value', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'balance' => -1,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
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

test('validates tag team id format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
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

test('validates tag team id exists', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
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

test('validates tag color format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Tag $tag */
    $tag = Tag::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'color' => 'invalid-color',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.tags.update', $tag->id), $tagData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.color field must be a valid hexadecimal color.',
                ],
            ],
        ]);
});
