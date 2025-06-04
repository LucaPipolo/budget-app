<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::createMaterializedView('category_balances', '
            SELECT
                category_id,
                SUM(CASE WHEN amount >= 0 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as total_outcome,
                SUM(amount) as balance
            FROM transactions
            GROUP BY category_id
        ');

        // Adds an unique index on category_id to allow concurrent refreshes.
        DB::statement('CREATE UNIQUE INDEX category_balances_category_id_idx ON category_balances (category_id)');
    }

    public function down(): void
    {
        Schema::dropMaterializedViewIfExists('category_balances');
    }
};
