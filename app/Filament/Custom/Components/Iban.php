<?php

declare(strict_types=1);

namespace App\Filament\Custom\Components;

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Str;

class Iban extends TextInput
{
    /**
     * Create a new form component instance.
     *
     * @param  string  $name  The name of the form component.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->maxLength(42)
            ->mask(RawJs::make(<<<'JS'
                (value) => {
                    const cleaned = value.replace(/\s/g, '');
                    return cleaned.replace(/(.{4})(?=.)/g, '$1 ');
                }
            JS))
            ->rules([
                function () {
                    /**
                     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
                     */
                    return function (string $attribute, string $value, Closure $fail): void {
                        if ($value && ! verify_iban(Str::remove(' ', $value))) {
                            $fail(__('iban.validations.not_valid'));
                        }
                    };
                },
            ])
            ->dehydrateStateUsing(null)
            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('iban.tooltips.hint'));
    }

    /**
     * Set a callback to dehydrate the state of the form component.
     *
     * @param  Closure|null  $callback  The callback to execute for dehydrating the state.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function dehydrateStateUsing(?Closure $callback): static
    {
        return parent::dehydrateStateUsing(fn (?string $state): string => Str::remove(' ', $state));
    }
}
