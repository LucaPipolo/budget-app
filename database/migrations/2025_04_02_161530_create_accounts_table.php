<?php

declare(strict_types=1);

use App\Enums\AccountOrigins;
use App\Enums\AccountTypes;
use Cknow\Money\Money;
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
        Schema::create('accounts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', array_column(AccountTypes::cases(), 'value'));
            $table->enum('origin', array_column(AccountOrigins::cases(), 'value'));
            $table->string('logo_path', 2048)->nullable();
            $table->bigInteger('balance')->default(0);
            $table->enum('currency', [array_keys(Money::getISOCurrencies())]);
            $table->string('iban', 34)->nullable();
            $table->string('swift', 11)->nullable();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->timestampsTz();

            $table->unique(['team_id', 'name']);
            $table->unique(['team_id', 'iban']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
