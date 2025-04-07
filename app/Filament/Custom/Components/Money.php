<?php

declare(strict_types=1);

namespace App\Filament\Custom\Components;

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;

class Money extends TextInput
{
    /**
     * The field used for currency.
     */
    protected string $currencyField = 'currency';

    /**
     * Create a new form component instance.
     *
     * @param  string  $name  The name of the form component.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->dehydrateStateUsing(fn (string $state): int => (int) preg_replace('/[^0-9]/', '', $state))
            ->formatStateUsing(null)
            ->afterStateUpdated(null)
            ->live()
            ->default(0);
    }

    /**
     * Set the field used for currency.
     *
     * @param  Closure|string  $currencyField  The field name or closure that returns the field name.
     */
    public function currencyField(Closure|string $currencyField): static
    {
        $this->currencyField = $currencyField;

        return $this;
    }

    /**
     * Set a callback to format the state of the money field.
     *
     * @param  Closure|null  $callback  The callback to execute for formatting the state.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function formatStateUsing(?Closure $callback): static
    {
        return parent::formatStateUsing(function (string $state, Get $get): string {
            return money((int) $state, $get($this->currencyField) ?: 'EUR')->format();
        });
    }

    /**
     * Set a callback to be executed after the state is updated.
     *
     * @param  Closure|null  $callback  The callback to execute after the state is updated.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function afterStateUpdated(?Closure $callback): static
    {
        return parent::afterStateUpdated(function (string $state, Set $set, Get $get): void {
            $numericValue = preg_replace('/[^0-9]/', '', $state ?: '0');
            $formattedValue = money($numericValue, $get($this->currencyField) ?: 'EUR')->format();
            $set($this->getName(), $formattedValue);
        });
    }
}
