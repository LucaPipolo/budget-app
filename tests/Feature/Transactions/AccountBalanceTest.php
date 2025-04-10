<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $team]);
    $this->category = Category::factory()->create(['team_id' => $team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $team]);

    $this->secondAccount = Account::factory()->create(['team_id' => $team]);
});

test('account balance is updated when transaction is created', function (): void {
    // Positive transaction (income).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(1000);

    // Negative transaction (outcome).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => -4000,
    ]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(-3000);
});

test('account balance is updated when transaction is updated', function (): void {
    $transaction = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(1000);

    $transaction->update(['amount' => 1500]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(1500);

    // Change the transaction amount to negative.
    $transaction->update(['amount' => -2000]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(-2000);
});

test('account balance is updated when transaction is deleted', function (): void {
    $transactions = Transaction::factory(2)->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(2000);

    $transactions[1]->delete();

    $this->account->refresh();

    expect($this->account->balance)->toBe(1000);
});

test('account balance is updated when transaction merchant is changed', function (): void {
    $transaction = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->account->refresh();
    $this->secondAccount->refresh();

    expect($this->account->balance)->toBe(1000)
        ->and($this->secondAccount->balance)->toBe(0);

    $transaction->update(['account_id' => $this->secondAccount->id]);

    $this->account->refresh();
    $this->secondAccount->refresh();

    expect($this->account->balance)->toBe(0)
        ->and($this->secondAccount->balance)->toBe(1000);
});
