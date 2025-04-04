<?php

declare(strict_types=1);

namespace App\Enums;

enum CategoryTypes: string
{
    /**
     * The available category type.
     */
    case INCOME = 'income';
    case OUTCOME = 'outcome';

    /**
     * Get the label for the enum value.
     *
     * @return string The label for the enum value.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::INCOME => __('categories.enums.type.income'),
            self::OUTCOME => __('categories.enums.type.outcome'),
        };
    }
}
