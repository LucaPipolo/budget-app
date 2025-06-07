<?php

declare(strict_types=1);

namespace App\Filament\Custom\Components;

use Filament\Forms\Components\TextInput;

class Swift extends TextInput
{
    /**
     * Create a new form component instance.
     *
     * @param  string  $name  The name of the form component.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->maxLength(11)
            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('swift.tooltips.hint'));
    }
}
