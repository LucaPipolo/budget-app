<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\MerchantBalance;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $team]);
    $this->category = Category::factory()->create(['team_id' => $team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $team]);

    $this->secondMerchant = Merchant::factory()->create(['team_id' => $team]);
});

test('merchant balances are updated when transaction is created', function (): void {
    // Positive transaction (income).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->merchant->refresh();
    expect($this->merchant->balance)->toBe(1000);

    $merchantBalance = MerchantBalance::where('merchant_id', $this->merchant->id)->first();

    expect($merchantBalance)->not->toBeNull()
        ->and($merchantBalance->total_income)->toBe(1000)
        ->and($merchantBalance->total_outcome)->toBe(0)
        ->and($merchantBalance->balance)->toBe(1000);

    // Negative transaction (outcome).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => -4000,
    ]);

    $this->merchant->refresh();
    expect($this->merchant->balance)->toBe(-3000);

    $merchantBalance = MerchantBalance::where('merchant_id', $this->merchant->id)->first();

    expect($merchantBalance)->not->toBeNull()
        ->and($merchantBalance->total_income)->toBe(1000)
        ->and($merchantBalance->total_outcome)->toBe(4000)
        ->and($merchantBalance->balance)->toBe(-3000);
});

test('merchant balances are updated when transaction is updated', function (): void {
    $transaction = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->merchant->refresh();
    expect($this->merchant->balance)->toBe(1000);

    $transaction->update(['amount' => 1500]);

    $this->merchant->refresh();
    expect($this->merchant->balance)->toBe(1500);

    $merchantBalance = MerchantBalance::where('merchant_id', $this->merchant->id)->first();

    expect($merchantBalance)->not->toBeNull()
        ->and($merchantBalance->total_income)->toBe(1500)
        ->and($merchantBalance->total_outcome)->toBe(0)
        ->and($merchantBalance->balance)->toBe(1500);

    // Change the transaction amount to negative.
    $transaction->update(['amount' => -2000]);

    $this->merchant->refresh();
    $merchantBalance->refresh();

    expect($this->merchant->balance)->toBe(-2000)
        ->and($merchantBalance)->not->toBeNull()
        ->and($merchantBalance->total_income)->toBe(0)
        ->and($merchantBalance->total_outcome)->toBe(2000)
        ->and($merchantBalance->balance)->toBe(-2000);
});

test('merchant balances are updated when transaction is deleted', function (): void {
    $transactions = Transaction::factory(2)->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->merchant->refresh();
    expect($this->merchant->balance)->toBe(2000);

    $merchantBalance = MerchantBalance::where('merchant_id', $this->merchant->id)->first();
    expect($merchantBalance->total_income)->toBe(2000);

    $transactions[1]->delete();

    $this->merchant->refresh();
    $merchantBalance->refresh();

    expect($this->merchant->balance)->toBe(1000)
        ->and($merchantBalance->balance)->toBe(1000);
});

test('merchant balances are updated when transaction merchant is changed', function (): void {
    $transaction = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->merchant->refresh();
    $this->secondMerchant->refresh();

    expect($this->merchant->balance)->toBe(1000)
        ->and($this->secondMerchant->balance)->toBe(0);

    $transaction->update(['merchant_id' => $this->secondMerchant->id]);

    $this->merchant->refresh();
    $this->secondMerchant->refresh();

    expect($this->merchant->balance)->toBe(0)
        ->and($this->secondMerchant->balance)->toBe(1000);

    $firstMerchantBalance = MerchantBalance::where('merchant_id', $this->merchant->id)->first();
    $secondMerchantBalance = MerchantBalance::where('merchant_id', $this->secondMerchant->id)->first();

    expect($firstMerchantBalance)->toBeNull()
        ->and($secondMerchantBalance)->not->toBeNull()
        ->and($secondMerchantBalance->total_income)->toBe(1000)
        ->and($secondMerchantBalance->total_outcome)->toBe(0)
        ->and($secondMerchantBalance->balance)->toBe(1000);
});
