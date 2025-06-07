<?php

declare(strict_types=1);

namespace App\Filament\Custom\Components;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Money\Currencies\ISOCurrencies;

class Currency extends Select
{
    /**
     * The field used for money.
     */
    protected string $moneyField = 'balance';

    /**
     * Create a new form component instance.
     *
     * @param  string  $name  The name of the form component.
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->options(function (): array {
                $options = [];

                foreach (new ISOCurrencies() as $currency) {
                    $code = $currency->getCode();
                    $options[$code] = $code;
                }

                return $options;
            })
            ->afterStateUpdated(null)
            ->default('EUR')
            ->searchable()
            ->live();
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
        /**
         * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
         */
        return parent::afterStateUpdated(function (?string $state, Set $set, Get $get): void {
            $moneyField = $get($this->moneyField);

            $numericValue = (int) preg_replace('/[^0-9]/', '', $moneyField ?: '0');
            $formattedValue = money($numericValue, $get($this->getName()))->format();

            $set($this->moneyField, $formattedValue);
        });
    }

    /**
     * Set the field that contains the money value.
     *
     * @param  Closure|string  $moneyField  The name of the money field.
     */
    public function moneyField(Closure|string $moneyField): static
    {
        $this->moneyField = $moneyField;

        return $this;
    }
}
