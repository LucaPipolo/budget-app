<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;

enum AccountTypes: string
{
    /**
     * The available account types.
     */
    case BANK = 'bank';
    case CASH = 'cash';
    case INVESTMENTS = 'investments';

    /**
     * Get the label for the enum value.
     *
     * @return string The label for the enum value.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::BANK => __('accounts.enums.type.bank'),
            self::CASH => __('accounts.enums.type.cash'),
            self::INVESTMENTS => __('accounts.enums.type.investments'),
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::BANK => Color::Green,
            self::CASH => Color::Blue,
            self::INVESTMENTS => Color::Purple,
        };
    }
}
