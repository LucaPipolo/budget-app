<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

test('validates account name is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 123,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account iban is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'iban' => 2251222463178612939052,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.iban field must be a string.',
                ],
            ],
        ]);
});

test('validates account swift is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'swift' => 123,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.swift field must be a string.',
                ],
            ],
        ]);
});

test('validates account name minimum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account name maximum length', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account balance is an integer', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'balance' => 1333.5,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account balance minimum value', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'balance' => -1,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account team id format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account team id exists', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account type is between accepted', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'type' => 'invalid-type',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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

test('validates account currency is between accepted', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'currency' => 'invalid-currency',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.currency is not a valid currency.',
                ],
            ],
        ]);
});

test('validates iban format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'iban' => 'invalid-iban',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.iban field format is invalid.',
                ],
            ],
        ]);
});

test('validates swift format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'swift' => 'invalid-swift',
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.swift field format is invalid.',
                ],
            ],
        ]);
});

test('validates account logo path format', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Account $account */
    $account = Account::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['create']);

    $logoPath = UploadedFile::fake()->image('account-logo.jpg')->path();

    $accountData = [
        'data' => [
            'attributes' => [
                'logoPath' => $logoPath,
            ],
        ],
    ];

    $this->actingAs($user)
        ->patchJson(route('api.v1.accounts.update', $account->id), $accountData)
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
                    'detail' => 'The data.attributes.logoPath field should start with \'accounts/\'.',
                ],
            ],
        ]);
});
