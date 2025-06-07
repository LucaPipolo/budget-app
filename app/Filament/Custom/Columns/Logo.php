<?php

declare(strict_types=1);

namespace App\Filament\Custom\Columns;

use Filament\Tables\Columns\ImageColumn;

class Logo extends ImageColumn
{
    /**
     * Create a new column instance.
     *
     * @param  string  $name  The name of the column.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->circular()
            ->size(40)
            ->extraImgAttributes(['class' => 'border border-gray-300']);
    }
}
