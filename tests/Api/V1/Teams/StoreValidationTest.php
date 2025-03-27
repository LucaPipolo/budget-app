<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('validates required team name', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['create']);

    $teamData = [
        'data' => [
            'attributes' => [
                'name' => '',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.teams.store'), $teamData)
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

test('validates team name is a string', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['create']);

    $teamData = [
        'data' => [
            'attributes' => [
                'name' => 123,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.teams.store'), $teamData)
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

test('validates team name minimum length', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['create']);

    $teamData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.teams.store'), $teamData)
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

test('validates team name maximum length', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['create']);

    $teamData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.teams.store'), $teamData)
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
