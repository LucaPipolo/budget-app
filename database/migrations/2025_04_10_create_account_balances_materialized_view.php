<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::createMaterializedView('account_balances', '
            SELECT
                account_id,
                SUM(CASE WHEN amount >= 0 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as total_outcome,
                SUM(amount) as balance
            FROM transactions
            GROUP BY account_id
        ');

        // Adds an unique index on account_id to allow concurrent refreshes.
        DB::statement('CREATE UNIQUE INDEX account_balances_account_id_idx ON account_balances (account_id)');
    }

    public function down(): void
    {
        Schema::dropMaterializedViewIfExists('account_balances');
    }
};
