<?php

declare(strict_types=1);

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    /**
     * @var string The Filament resource.
     */
    protected static string $resource = AccountResource::class;

    /**
     * Add required data before saving.
     *
     * @param  array  $data  The form data.
     *
     * @return array The modified form data.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = auth()->user()->current_team_id;
        $data['origin'] = 'web';

        return $data;
    }
}
