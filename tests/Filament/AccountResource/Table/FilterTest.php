<?php

declare(strict_types=1);

use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

test('can filter accounts by type', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory()->count(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $type = $accounts->first()->type;

    livewire(ListAccounts::class)
        ->filterTable('type', $type)
        ->assertCanSeeTableRecords($accounts->where('type', $type))
        ->assertCanNotSeeTableRecords($accounts->where('type', '!=', $type));
});
