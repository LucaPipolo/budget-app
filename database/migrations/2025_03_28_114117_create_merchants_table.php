<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Schema\Timescale\Actions\CreateCompressionPolicy;
use Tpetry\PostgresqlEnhanced\Schema\Timescale\Actions\CreateHypertable;
use Tpetry\PostgresqlEnhanced\Schema\Timescale\Actions\CreateRefreshPolicy;
use Tpetry\PostgresqlEnhanced\Schema\Timescale\Actions\EnableCompression;
use Tpetry\PostgresqlEnhanced\Schema\Timescale\CaggBlueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createExtensionIfNotExists('timescaledb');

        Schema::create('merchants', function (Blueprint $table): void {
            //            $table->uuid('id')->primary();
            //            $table->identity();
            $table->uuid('id');
            $table->string('name');
            $table->string('logo_path', 2048)->nullable();
            $table->bigInteger('balance')->default(0);
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->timestampsTz();

            $table->primary(['id', 'updated_at']);
            //            $table->unique(['team_id', 'created_at']);

            $table->timescale(
                new CreateHypertable('updated_at', '1 day'),
            );
        });

        Schema::continuousAggregate('merchants_balance_sum', function (CaggBlueprint $table): void {
            $table->as("
                SELECT
                    time_bucket('1 hour', updated_at) AS bucket,
                    team_id,
                    SUM(balance) AS balance
                FROM merchants
                GROUP BY bucket, team_id
            ");
            $table->realtime();
            $table->index(['team_id']);

            $table->timescale(
                new CreateRefreshPolicy('5 minutes', '1 days', '2 hours'),
                new EnableCompression(),
                new CreateCompressionPolicy('2 days'),
            );
        });
        //        new CreateHypertable();
        //
        //        Schema::hypertable('merchants', 'created_at', [
        //            'chunk_time_interval' => '1 month',
        //            'migrate_data' => true, // Migrate existing data into chunks
        //        ]);

        //        new CreateHypertable(
        //            'chunk_time_interval' => '1 month',
        //            'migrate_data' => true,
        //        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
