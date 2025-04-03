<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountOrigins: string
{
    /**
     * The available account origins.
     */
    case WEB = 'web';
    case API = 'api';
    case EXTERNAL = 'external';

    /**
     * Get the label for the enum value.
     *
     * @return string The label for the enum value.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::WEB => __('accounts.enums.type.web'),
            self::API => __('accounts.enums.type.api'),
            self::EXTERNAL => __('accounts.enums.type.external'),
        };
    }
}
