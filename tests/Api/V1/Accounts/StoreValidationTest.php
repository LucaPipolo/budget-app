<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

test('validates required account name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => '',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

test('validates required account type', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => '',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.type field is required.',
                ],
            ],
        ]);
});

test('validates required account currency', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => '',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.currency field is required.',
                ],
            ],
        ]);
});

test('validates required account team id', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => '',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

test('validates account name is a string', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 123,
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 2251222463178612939052,
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 123,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'ab',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => str_repeat('a', 256),
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 1333.5,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => -1,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'invalid-type',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'invalid-currency',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'invalid-iban',
                'swift' => 'UFCLDWFV',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'invalid-swift',
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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

    Sanctum::actingAs($user, ['create']);

    $logoPath = UploadedFile::fake()->image('account-logo.jpg')->path();

    $accountData = [
        'data' => [
            'attributes' => [
                'name' => 'New Test Account',
                'type' => 'bank',
                'balance' => 33,
                'currency' => 'EUR',
                'iban' => 'ES2251222463178612939052',
                'swift' => 'UFCLDWFV',
                'logoPath' => $logoPath,
                'teamId' => $user->currentTeam->id,
            ],
        ],
    ];

    $this->actingAs($user)
        ->postJson(route('api.v1.accounts.store'), $accountData)
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
