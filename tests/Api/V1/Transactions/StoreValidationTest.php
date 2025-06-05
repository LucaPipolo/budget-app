<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->user = User::factory()->withPersonalTeam()->create();
    $this->team = $this->user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $this->team]);
    $this->category = Category::factory()->create(['team_id' => $this->team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $this->team]);
});

test('validates required transaction amount', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => '',
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.amount field is required.',
                ],
            ],
        ]);
});

test('validates required transaction date', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.date field is required.',
                ],
            ],
        ]);
});

test('validates required transaction account id', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => '',
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.account id field is required.',
                ],
            ],
        ]);
});

test('validates required transaction merchant id', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => '',
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.merchant id field is required.',
                ],
            ],
        ]);
});

test('validates required transaction category id', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => '',
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.category id field is required.',
                ],
            ],
        ]);
});

test('validates required transaction team id', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => '',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
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

test('validates transaction amount is an integer', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000.32,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.amount field must be an integer.',
                ],
            ],
        ]);
});

test('validates transaction date respects the required format', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.date field must match the format Y-m-d H:i:sP.',
                ],
            ],
        ]);
});

test('validates transaction notes is a string', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 123,
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.notes field must be a string.',
                ],
            ],
        ]);
});

test('validates transaction notes maximum length', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => str_repeat('a', 256),
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.notes field must not be greater than 255 characters.',
                ],
            ],
        ]);
});

test('validates transaction account id format', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => 'invalid-uuid',
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.account id field must be a valid UUID.',
                ],
            ],
        ]);
});

test('validates transaction account id exists', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => '00000000-0000-0000-0000-000000000000',
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The selected data.attributes.account id is invalid.',
                ],
            ],
        ]);
});

test('validates transaction merchant id format', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => 'invalid-uuid',
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.merchant id field must be a valid UUID.',
                ],
            ],
        ]);
});

test('validates transaction merchant id exists', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => '00000000-0000-0000-0000-000000000000',
                'categoryId' => $this->category->id,
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The selected data.attributes.merchant id is invalid.',
                ],
            ],
        ]);
});

test('validates transaction category id format', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => 'invalid-uuid',
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The data.attributes.category id field must be a valid UUID.',
                ],
            ],
        ]);
});

test('validates transaction category id exists', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => '00000000-0000-0000-0000-000000000000',
                'teamId' => $this->team,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'status' => '422',
                    'title' => 'Validation Error.',
                    'detail' => 'The selected data.attributes.category id is invalid.',
                ],
            ],
        ]);
});

test('validates transaction team id format', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
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

test('validates transaction team id exists', function (): void {
    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000,
                'date' => '2025-05-05 14:05:29+00',
                'notes' => 'A NEW beautiful note.',
                'accountId' => $this->account->id,
                'merchantId' => $this->merchant->id,
                'categoryId' => $this->category->id,
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->postJson(route('api.v1.transactions.store'), $transactionData)
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
