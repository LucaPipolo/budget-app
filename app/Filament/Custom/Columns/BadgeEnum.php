<?php

declare(strict_types=1);

namespace App\Filament\Custom\Columns;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class BadgeEnum extends TextColumn
{
    protected string $enum;

    /**
     * Create a new column instance.
     *
     * @param  string  $name  The name of the column.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->formatStateUsing(fn (string $state): string => mb_ucfirst($state))
            ->badge()
            ->color(null);
    }

    /**
     * Set the color of the badge based on the enum state.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function color(Closure|array|string|bool|null $color): static
    {
        return parent::color(fn (string $state): array => Str::studly($this->enum)::from($state)->getColor());
    }

    /**
     * Set the enum class for the select component.
     *
     * @param  Closure|string  $enum  The enum class.
     */
    public function enum(Closure|string $enum): static
    {
        $this->enum = $enum;

        return $this;
    }
}
