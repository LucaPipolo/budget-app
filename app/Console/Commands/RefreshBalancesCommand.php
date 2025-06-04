<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AccountBalance;
use App\Models\CategoryBalance;
use App\Models\MerchantBalance;
use Illuminate\Console\Command;

class RefreshBalancesCommand extends Command
{
    protected $signature = 'balances:refresh
                            {--accounts : Update only the accounts balances}
                            {--merchants : Update only the merchant balances}
                            {--categories : Update only the categories balances}';

    protected $description = 'Update the materialized views for account, merchant, and category balances.';

    public function handle(): int
    {
        $refreshMerchants = $this->option('merchants');
        $refreshAccounts = $this->option('accounts');
        $refreshCategories = $this->option('categories');

        $refreshAll = ! $refreshAccounts && ! $refreshMerchants && ! $refreshCategories;

        try {
            if ($refreshAccounts || $refreshAll) {
                $this->info('Updating the materialized view account_balances...');
                AccountBalance::refreshView();
                $this->info('Materialized view account_balances updated successfully.');
            }

            if ($refreshMerchants || $refreshAll) {
                $this->info('Updating the materialized view merchant_balances...');
                MerchantBalance::refreshView();
                $this->info('Materialized view merchant_balances updated successfully.');
            }

            if ($refreshCategories || $refreshAll) {
                $this->info('Updating the materialized view category_balances...');
                CategoryBalance::refreshView();
                $this->info('Materialized view category_balances updated successfully.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error! Was not possible to update the view: ' . $e->getMessage());

            return 1;
        }
    }
}
