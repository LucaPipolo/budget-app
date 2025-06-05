<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\AccountBalance;
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

test('account balances are updated when transaction is created', function (): void {
    // Positive transaction (income).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(1000);

    $accountBalance = AccountBalance::where('account_id', $this->account->id)->first();

    expect($accountBalance)->not->toBeNull()
        ->and($accountBalance->total_income)->toBe(1000)
        ->and($accountBalance->total_outcome)->toBe(0)
        ->and($accountBalance->balance)->toBe(1000);

    // Negative transaction (outcome).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => -4000,
    ]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(-3000);

    $accountBalance = AccountBalance::where('account_id', $this->account->id)->first();

    expect($accountBalance)->not->toBeNull()
        ->and($accountBalance->total_income)->toBe(1000)
        ->and($accountBalance->total_outcome)->toBe(4000)
        ->and($accountBalance->balance)->toBe(-3000);
});

test('account balances are updated when transaction is updated', function (): void {
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

    $accountBalance = AccountBalance::where('account_id', $this->account->id)->first();

    expect($accountBalance)->not->toBeNull()
        ->and($accountBalance->total_income)->toBe(1500)
        ->and($accountBalance->total_outcome)->toBe(0)
        ->and($accountBalance->balance)->toBe(1500);

    // Change the transaction amount to negative.
    $transaction->update(['amount' => -2000]);

    $this->account->refresh();
    $accountBalance->refresh();

    expect($this->account->balance)->toBe(-2000)
        ->and($accountBalance)->not->toBeNull()
        ->and($accountBalance->total_income)->toBe(0)
        ->and($accountBalance->total_outcome)->toBe(2000)
        ->and($accountBalance->balance)->toBe(-2000);
});

test('account balances are updated when transaction is deleted', function (): void {
    $transactions = Transaction::factory(2)->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->account->refresh();
    expect($this->account->balance)->toBe(2000);

    $accountBalance = AccountBalance::where('account_id', $this->account->id)->first();
    expect($accountBalance->total_income)->toBe(2000);

    $transactions[1]->delete();

    $this->account->refresh();
    $accountBalance->refresh();

    expect($this->account->balance)->toBe(1000)
        ->and($accountBalance->balance)->toBe(1000);
});

test('account balances are updated when transaction merchant is changed', function (): void {
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

    $firstAccountBalance = AccountBalance::where('account_id', $this->account->id)->first();
    $secondAccountBalance = AccountBalance::where('account_id', $this->secondAccount->id)->first();

    expect($firstAccountBalance)->toBeNull()
        ->and($secondAccountBalance)->not->toBeNull()
        ->and($secondAccountBalance->total_income)->toBe(1000)
        ->and($secondAccountBalance->total_outcome)->toBe(0)
        ->and($secondAccountBalance->balance)->toBe(1000);
});
