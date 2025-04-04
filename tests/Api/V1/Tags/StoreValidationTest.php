<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('validates required tag name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => '',
                'balance' => 1333,
                'color' => '#f3f4f6',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.name field is required.',
                ],
            ],
        ]);
});

test('validates required tag team id', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Tag',
                'balance' => 1333,
                'color' => '#f3f4f6',
                'teamId' => '',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.team id field is required.',
                ],
            ],
        ]);
});

test('validates tag name is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 123,
                'balance' => 1333,
                'color' => '#f3f4f6',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
                'balance' => 1333,
                'color' => '#f3f4f6',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
                'balance' => 1333,
                'color' => '#f3f4f6',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Tag',
                'balance' => 1333.5,
                'color' => '#f3f4f6',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Tag',
                'balance' => -1,
                'color' => '#f3f4f6',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Tag',
                'balance' => 1333,
                'color' => '#f3f4f6',
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Tag',
                'balance' => 1333,
                'color' => '#f3f4f6',
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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

    Sanctum::actingAs($user, ['create']);

    $tagData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Tag',
                'balance' => 1333,
                'color' => 'invalid-color',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.tags.store'), $tagData)
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
