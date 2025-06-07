<?php

declare(strict_types=1);

namespace App\Filament\Custom\Columns;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class Money extends TextColumn
{
    /**
     * Create a new column instance.
     *
     * @param  string  $name  The name of the column.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->formatStateUsing(function (string $state, Model $record): string {
                return money($state, $record->currency ?? 'EUR')->format();
            })
            ->sortable();
    }
}
