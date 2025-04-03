<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

test('validates merchant name is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 123,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'balance' => 1333.5,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'balance' => -1,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $merchantData = [
        'data' => [
            'attributes' => [
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
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

test('validates merchant logo path format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Merchant $merchant */
    $merchant = Merchant::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $logoPath = UploadedFile::fake()->image('merchant-logo.jpg')->path();

    $merchantData = [
        'data' => [
            'attributes' => [
                'logoPath' => $logoPath,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.merchants.update', $merchant->id), $merchantData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.logoPath field refers to a non existing file.',
                ],
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.logoPath field should start with \'merchants/\'.',
                ],
            ],
        ]);
});
