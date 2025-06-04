<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->user = User::factory()->withPersonalTeam()->create();
    $this->team = $this->user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $this->team]);
    $this->category = Category::factory()->create(['team_id' => $this->team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $this->team]);
});

test('validates transaction amount is an integer', function (): void {
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'amount' => 3000.32,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'date' => '2025-05-05 14:05',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'notes' => 123,
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'notes' => str_repeat('a', 256),
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'accountId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'accountId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'merchantId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'merchantId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'categoryId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'categoryId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'teamId' => 'invalid-uuid',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->team,
    ]);

    Sanctum::actingAs($this->user, ['create']);

    $transactionData = [
        'data' => [
            'attributes' => [
                'teamId' => '00000000-0000-0000-0000-000000000000',
            ],
        ],
    ];

    $this->actingAs($this->user)
        ->patchJson(route('api.v1.transactions.update', $transaction->id), $transactionData)
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
