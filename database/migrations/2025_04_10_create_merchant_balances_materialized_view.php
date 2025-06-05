<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::createMaterializedView('merchant_balances', '
            SELECT
                merchant_id,
                SUM(CASE WHEN amount >= 0 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as total_outcome,
                SUM(amount) as balance
            FROM transactions
            GROUP BY merchant_id
        ');

        // Adds an unique index on merchant_id to allow concurrent refreshes.
        DB::statement('CREATE UNIQUE INDEX merchant_balances_merchant_id_idx ON merchant_balances (merchant_id)');
    }

    public function down(): void
    {
        Schema::dropMaterializedViewIfExists('merchant_balances');
    }
};
