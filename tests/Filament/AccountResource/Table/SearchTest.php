<?php

declare(strict_types=1);

use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

test('can search accounts by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory()->count(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $name = $accounts->first()->name;

    livewire(ListAccounts::class)
        ->searchTable($name)
        ->assertCanSeeTableRecords($accounts->where('name', $name))
        ->assertCanNotSeeTableRecords($accounts->where('name', '!=', $name));
});

test('can search accounts by IBAN', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory()->count(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $iban = $accounts->last()->iban;

    livewire(ListAccounts::class)
        ->searchTable($iban)
        ->assertCanSeeTableRecords($accounts->where('iban', $iban))
        ->assertCanNotSeeTableRecords($accounts->where('iban', '!=', $iban));
});

test('can search accounts by SWIFT/BIC', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory()->count(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $swift = $accounts->first()->swift;

    livewire(ListAccounts::class)
        ->searchTable($swift)
        ->assertCanSeeTableRecords($accounts->where('swift', $swift))
        ->assertCanNotSeeTableRecords($accounts->where('swift', '!=', $swift));
});
