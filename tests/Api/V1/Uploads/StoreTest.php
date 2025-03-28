<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

beforeEach(function (): void {
    Storage::fake('public');
});

assertEndpointRequiresAuthentication('api.v1.uploads.store');

test('user with "create" token can upload a file', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $response = $this->postJson(route('api.v1.uploads.store'), [
        'entity' => 'merchants',
        'file' => UploadedFile::fake()->image('merchant-logo.jpg'),
    ]);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'path',
                    'url',
                ],
            ],
        ]);

    $path = $response->json('data.attributes.path');

    Storage::disk('public')->assertExists($path);
});

test('denies creation to user without "create" token', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['read', 'update', 'delete']);

    $response = $this->postJson(route('api.v1.uploads.store'), [
        'entity' => 'merchants',
        'file' => UploadedFile::fake()->image('merchant-logo.jpg'),
    ]);

    $response->assertStatus(403);
});
