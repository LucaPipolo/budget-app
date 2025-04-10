<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MerchantBalance;
use Illuminate\Console\Command;

class RefreshBalancesCommand extends Command
{
    protected $signature = 'balances:refresh
                            {--merchants : Update only the merchant balances}';

    protected $description = 'Update the materialized views for merchant balances.';

    public function handle(): int
    {
        $refreshMerchants = $this->option('merchants');

        $refreshAll = ! $refreshMerchants;

        try {
            if ($refreshMerchants || $refreshAll) {
                $this->info('Updating the materialized view merchant_balances...');
                MerchantBalance::refreshView();
                $this->info('Materialized view merchant_balances updated successfully.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error! Was not possible to update the view: ' . $e->getMessage());

            return 1;
        }
    }
}
