<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

beforeEach(function (): void {
    $this->user = User::factory()->withPersonalTeam()->create();
    $this->team = $this->user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $this->team]);
    $this->category = Category::factory()->create(['team_id' => $this->team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $this->team]);
});

assertEndpointRequiresAuthentication('api.v1.merchants.relationships.transactions', 'getJson', ['merchant' => '1']);

test('user with "read" token can see merchant transactions relationships', function (): void {
    $transactions = Transaction::factory()->count(2)->create([
        'merchant_id' => $this->merchant,
    ]);

    Sanctum::actingAs($this->user, ['read']);

    $response = $this->actingAs($this->user)
        ->getJson(route('api.v1.merchants.relationships.transactions', ['merchant' => $this->merchant]))
        ->assertStatus(200)
        ->assertJsonStructure([
            'links' => [
                'self',
                'related',
            ],
            'data' => [
                '*' => [
                    'type',
                    'id',
                ],
            ],
        ]);

    $transactionIds = $transactions->pluck('id')->toArray();
    sort($transactionIds);

    $responseTransactionIds = collect($response->json('data'))->pluck('id')->toArray();
    sort($responseTransactionIds);

    $this->assertEquals($transactionIds, $responseTransactionIds);
    $this->assertCount(2, $response->json('data'));
});

test('denies access to merchant transactions relationships without "read" token', function (): void {
    Sanctum::actingAs($this->user, ['create', 'update', 'delete']);

    $this->actingAs($this->user)
        ->getJson(route('api.v1.merchants.relationships.transactions', ['merchant' => $this->merchant]))
        ->assertStatus(403);
});
