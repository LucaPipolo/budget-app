<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    /**
     * Update balances for a new transaction.
     *
     * @param  Transaction  $transaction  The created transaction.
     */
    public function updateBalancesForNewTransaction(Transaction $transaction): void
    {
        $amount = $transaction->amount;

        $this->updateAccountBalance($transaction->account_id, $amount);
        $this->updateMerchantBalance($transaction->merchant_id, $amount);
        $this->updateCategoryBalance($transaction->category_id, $amount);
    }

    /**
     * Update balances for an updated transaction.
     *
     * @param  Transaction  $transaction  The updated transaction.
     * @param  Transaction  $transactionOriginal  The original transaction before the update.
     */
    public function updateBalancesForUpdatedTransaction(
        Transaction $transaction,
        Transaction $transactionOriginal,
    ): void {
        $oldAmount = $transactionOriginal->amount;
        $newAmount = $transaction->amount;

        $originalAccountId = $transactionOriginal->account_id;
        if ($originalAccountId !== $transaction->account_id) {
            if ($originalAccountId) {
                $this->updateAccountBalance($originalAccountId, -$oldAmount);
            }
            $this->updateAccountBalance($transaction->account_id, $newAmount);
        } elseif ($oldAmount !== $newAmount) {
            $this->updateAccountBalance($transaction->account_id, $newAmount - $oldAmount);
        }

        $originalMerchantId = $transactionOriginal->merchant_id;
        if ($originalMerchantId !== $transaction->merchant_id) {
            if ($originalMerchantId) {
                $this->updateMerchantBalance($originalMerchantId, -$oldAmount);
            }
            $this->updateMerchantBalance($transaction->merchant_id, $newAmount);
        } elseif ($oldAmount !== $newAmount) {
            $this->updateMerchantBalance($transaction->merchant_id, $newAmount - $oldAmount);
        }

        $originalCategoryId = $transactionOriginal->category_id;
        if ($originalCategoryId !== $transaction->category_id) {
            if ($originalCategoryId) {
                $this->updateCategoryBalance($originalCategoryId, -$oldAmount);
            }
            $this->updateCategoryBalance($transaction->category_id, $newAmount);
        } elseif ($oldAmount !== $newAmount) {
            $this->updateCategoryBalance($transaction->category_id, $newAmount - $oldAmount);
        }
    }

    /**
     * Update balances for a deleted transaction.
     *
     * @param  Transaction  $transaction  The deleted transaction.
     */
    public function updateBalancesForDeletedTransaction(Transaction $transaction): void
    {
        $amount = -$transaction->amount;

        try {
            $this->updateAccountBalance($transaction->account_id, $amount);
            $this->updateMerchantBalance($transaction->merchant_id, $amount);
            $this->updateCategoryBalance($transaction->category_id, $amount);
        } catch (QueryException $e) {
            Log::error('Error! Was not possible to update balances: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'account_id' => $transaction->account_id,
            ]);
            throw $e;
        }
    }

    /**
     * Update the account balance.
     *
     * @param  string  $accountId  The ID of the account to update.
     * @param  int  $amount  The amount to add or subtract from the balance.
     */
    private function updateAccountBalance(string $accountId, int $amount): void
    {
        Account::where('id', $accountId)
            ->lockForUpdate()
            ->update(['balance' => DB::raw("balance + {$amount}")]);
    }

    /**
     * Update the merchant balance.
     *
     * @param  string  $merchantId  The ID of the merchant to update.
     * @param  int  $amount  The amount to add or subtract from the balance.
     */
    private function updateMerchantBalance(string $merchantId, int $amount): void
    {
        Merchant::where('id', $merchantId)
            ->lockForUpdate()
            ->update(['balance' => DB::raw("balance + {$amount}")]);
    }

    /**
     * Update the category balance.
     *
     * @param  string  $categoryId  The ID of the category to update.
     * @param  int  $amount  The amount to add or subtract from the balance.
     */
    private function updateCategoryBalance(string $categoryId, int $amount): void
    {
        Category::where('id', $categoryId)
            ->lockForUpdate()
            ->update(['balance' => DB::raw("balance + {$amount}")]);
    }
}
