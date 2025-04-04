<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshTransactionSumsView extends Command
{
    protected $signature = 'db:refresh-transaction-sums-view';

    protected $description = 'Refresh the transaction sums materialized view';

    public function handle(): void
    {
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY merchants_sum');
        $this->info('Materialized view refreshed successfully.');
    }
}
