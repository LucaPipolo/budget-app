<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\CategoryBalance;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $team]);
    $this->category = Category::factory()->create(['team_id' => $team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $team]);

    $this->secondCategory = Category::factory()->create(['team_id' => $team]);
});

test('category balances are updated when transaction is created', function (): void {
    // Positive transaction (income).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->category->refresh();
    expect($this->category->balance)->toBe(1000);

    $categoryBalance = CategoryBalance::where('category_id', $this->category->id)->first();

    expect($categoryBalance)->not->toBeNull()
        ->and($categoryBalance->total_income)->toBe(1000)
        ->and($categoryBalance->total_outcome)->toBe(0)
        ->and($categoryBalance->balance)->toBe(1000);

    // Negative transaction (outcome).
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => -4000,
    ]);

    $this->category->refresh();
    expect($this->category->balance)->toBe(-3000);

    $categoryBalance = CategoryBalance::where('category_id', $this->category->id)->first();

    expect($categoryBalance)->not->toBeNull()
        ->and($categoryBalance->total_income)->toBe(1000)
        ->and($categoryBalance->total_outcome)->toBe(4000)
        ->and($categoryBalance->balance)->toBe(-3000);
});

test('category balances are updated when transaction is updated', function (): void {
    $transaction = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->category->refresh();
    expect($this->category->balance)->toBe(1000);

    $transaction->update(['amount' => 1500]);

    $this->category->refresh();
    expect($this->category->balance)->toBe(1500);

    $categoryBalance = CategoryBalance::where('category_id', $this->category->id)->first();

    expect($categoryBalance)->not->toBeNull()
        ->and($categoryBalance->total_income)->toBe(1500)
        ->and($categoryBalance->total_outcome)->toBe(0)
        ->and($categoryBalance->balance)->toBe(1500);

    // Change the transaction amount to negative.
    $transaction->update(['amount' => -2000]);

    $this->category->refresh();
    $categoryBalance->refresh();

    expect($this->category->balance)->toBe(-2000)
        ->and($categoryBalance)->not->toBeNull()
        ->and($categoryBalance->total_income)->toBe(0)
        ->and($categoryBalance->total_outcome)->toBe(2000)
        ->and($categoryBalance->balance)->toBe(-2000);
});

test('category balances are updated when transaction is deleted', function (): void {
    $transactions = Transaction::factory(2)->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->category->refresh();
    expect($this->category->balance)->toBe(2000);

    $categoryBalance = CategoryBalance::where('category_id', $this->category->id)->first();
    expect($categoryBalance->total_income)->toBe(2000);

    $transactions[1]->delete();

    $this->category->refresh();
    $categoryBalance->refresh();

    expect($this->category->balance)->toBe(1000)
        ->and($categoryBalance->balance)->toBe(1000);
});

test('category balances are updated when transaction merchant is changed', function (): void {
    $transaction = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'merchant_id' => $this->merchant->id,
        'amount' => 1000,
    ]);

    $this->category->refresh();
    $this->secondCategory->refresh();

    expect($this->category->balance)->toBe(1000)
        ->and($this->secondCategory->balance)->toBe(0);

    $transaction->update(['category_id' => $this->secondCategory->id]);

    $this->category->refresh();
    $this->secondCategory->refresh();

    expect($this->category->balance)->toBe(0)
        ->and($this->secondCategory->balance)->toBe(1000);

    $firstCategoryBalance = CategoryBalance::where('category_id', $this->category->id)->first();
    $secondCategoryBalance = CategoryBalance::where('category_id', $this->secondCategory->id)->first();

    expect($firstCategoryBalance)->toBeNull()
        ->and($secondCategoryBalance)->not->toBeNull()
        ->and($secondCategoryBalance->total_income)->toBe(1000)
        ->and($secondCategoryBalance->total_outcome)->toBe(0)
        ->and($secondCategoryBalance->balance)->toBe(1000);
});
