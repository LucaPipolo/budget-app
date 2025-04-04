<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createMaterializedView('merchants_sum', "
            SELECT
                team_id,
                SUM(balance) AS total_balance,
                DATE_TRUNC('day', updated_at) AS day
            FROM merchants
            GROUP BY team_id, DATE_TRUNC('day', updated_at)
        ");
        DB::statement('CREATE UNIQUE INDEX idx_transaction_sums_category_day ON merchants_sum (team_id, total_balance, day)');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropMaterializedView('merchants_sum');
    }
};
