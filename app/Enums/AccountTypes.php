<?php

declare(strict_types=1);

namespace App\Enums;

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
}
