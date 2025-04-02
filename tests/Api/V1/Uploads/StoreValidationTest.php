<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

test('validates required params', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $this
        ->postJson(route('api.v1.uploads.store'), [])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The entity field is required.',
                ],
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The file field is required.',
                ],
            ],
        ]);
});

test('validates upload entity', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $this
        ->postJson(route('api.v1.uploads.store'), [
            'entity' => 'invalid-type',
            'file' => UploadedFile::fake()->image('merchant-logo.jpg'),
        ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The selected entity is invalid.',
                ],
            ],
        ]);
});

test('validates max file size', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $this
        ->postJson(route('api.v1.uploads.store'), [
            'entity' => 'merchants',
            'file' => UploadedFile::fake()->create('large-file.jpg', 6000),
        ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The file field must not be greater than 5120 kilobytes.',
                ],
            ],
        ]);
});
