<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('validates required merchant name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => '',
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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

test('validates merchant name is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 123,
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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

test('validates merchant name minimum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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

test('validates merchant name maximum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
                'balance' => 1333,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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

test('validates merchant balance is an integer', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Merchant',
                'balance' => 1333.5,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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

test('validates merchant balance minimum value', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Merchant',
                'balance' => -1,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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

test('validates merchant team id format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Merchant',
                'balance' => 1333,
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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

test('validates merchant team id exists', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Merchant',
                'balance' => 1333,
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.merchants.store'), $merchantData)
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
