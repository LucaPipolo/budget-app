<?php

declare(strict_types=1);

use App\Enums\CategoryTypes;
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
        Schema::create('categories', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', array_column(CategoryTypes::cases(), 'value'));
            $table->bigInteger('balance')->default(0);
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->timestampsTz();

            $table->unique(['team_id', 'name', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
