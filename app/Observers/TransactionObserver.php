<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AccountBalance;
use App\Models\CategoryBalance;
use App\Models\MerchantBalance;
use App\Models\Transaction;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionObserver
{
    protected BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Handle the transaction "created" event.
     *
     * @param  Transaction  $transaction  The transaction instance that was created.
     *
     * @throws Throwable
     */
    public function created(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $this->balanceService->updateBalancesForNewTransaction($transaction);
            $this->refreshViews();
        });
    }

    /**
     * Handle the transaction "updated" event.
     *
     * @param  Transaction  $transaction  The transaction instance that was created.
     *
     * @throws Throwable
     */
    public function updated(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $originalAttributes = $transaction->getOriginal();
            $originalTransaction = new Transaction();
            $originalTransaction->setRawAttributes($originalAttributes);

            $this->balanceService->updateBalancesForUpdatedTransaction(
                $transaction,
                $originalTransaction
            );
        });

        $this->refreshViews();
    }

    /**
     * Handle the transaction "deleted" event.
     *
     * @param  Transaction  $transaction  The transaction instance that was created.
     *
     * @throws Throwable
     */
    public function deleted(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $this->balanceService->updateBalancesForDeletedTransaction($transaction);
            $this->refreshViews();
        });
    }

    /**
     * Handle the transaction "restored" event.
     *
     * @param  Transaction  $transaction  The transaction instance that was created.
     *
     * @throws Throwable
     */
    public function restored(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $this->balanceService->updateBalancesForNewTransaction($transaction);
            $this->refreshViews();
        });
    }

    /**
     * Refresh balances materialized views.
     */
    public function refreshViews(): void
    {
        AccountBalance::refreshView();
        MerchantBalance::refreshView();
        CategoryBalance::refreshView();
    }
}
