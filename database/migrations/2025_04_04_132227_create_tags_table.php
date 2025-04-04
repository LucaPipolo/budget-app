<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropDomainIfExists('hex_color');
        Schema::createDomain('hex_color', 'char(7)', "VALUE ~ '^#[0-9a-fA-F]{6}$'");

        Schema::create('tags', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->bigInteger('balance')->default(0);
            $table->domain('color', 'hex_color')->nullable();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->timestampsTz();

            $table->unique(['team_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
