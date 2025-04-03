<?php

declare(strict_types=1);

namespace App\Enums;

enum UploadEntities: string
{
    /**
     * The available upload entities.
     */
    case ACCOUNTS = 'accounts';
    case MERCHANTS = 'merchants';

    /**
     * Get the label for the enum value.
     *
     * @return string The label for the enum value.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ACCOUNTS => __('uploads.enums.type.accounts'),
            self::MERCHANTS => __('uploads.enums.type.merchants'),
        };
    }
}
