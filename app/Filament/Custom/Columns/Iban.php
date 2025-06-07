<?php

declare(strict_types=1);

namespace App\Filament\Custom\Columns;

use Filament\Tables\Columns\TextColumn;

class Iban extends TextColumn
{
    /**
     * Create a new column instance.
     *
     * @param  string  $name  The name of the column.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->formatStateUsing(function (?string $state) {
                return trim(chunk_split(str_replace(' ', '', $state), 4, ' '));
            })
            ->searchable();
    }
}
