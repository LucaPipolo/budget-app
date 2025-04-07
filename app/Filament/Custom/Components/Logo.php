<?php

declare(strict_types=1);

namespace App\Filament\Custom\Components;

use Filament\Forms\Components\FileUpload;

class Logo extends FileUpload
{
    /**
     * Create a new form component instance.
     *
     * @param  string  $name  The name of the form component.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->columnSpan('full')
            ->image()
            ->imageEditor()
            ->avatar()
            ->disk('public')
            ->visibility('private')
            ->maxSize(1024)
            ->acceptedFileTypes(['image/webp',  'image/avif', 'image/jpeg', 'image/png']);
    }
}
