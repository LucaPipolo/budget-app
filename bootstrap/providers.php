<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\JetstreamServiceProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    AppPanelProvider::class,
    FortifyServiceProvider::class,
    JetstreamServiceProvider::class,
    TelescopeServiceProvider::class,
];
