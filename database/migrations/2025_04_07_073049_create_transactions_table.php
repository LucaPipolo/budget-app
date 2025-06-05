<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->bigInteger('amount')->default(0);
            $table->timestampTz('date');
            $table->string('notes')->nullable();
            $table->foreignUuid('account_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
