<?php

declare(strict_types=1);

use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

test('page can be rendered', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    livewire(ListAccounts::class)->assertSuccessful();
});

test('list contains all columns', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    Account::factory()->count(4)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    livewire(ListAccounts::class)
        ->assertCanRenderTableColumn('logo_path')
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('type')
        ->assertCanRenderTableColumn('balance')
        ->set('toggledTableColumns', ['iban' => true])
        ->assertCanRenderTableColumn('iban')
        ->set('toggledTableColumns', ['swift' => true])
        ->assertCanRenderTableColumn('swift')
        ->set('toggledTableColumns', ['created_at' => true])
        ->assertCanRenderTableColumn('created_at')
        ->set('toggledTableColumns', ['updated_at' => true])
        ->assertCanRenderTableColumn('updated_at');
});

test('balance is properly formatted', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $account = Account::factory()->create([
        'balance' => 123456,
        'currency' => 'EUR',
        'team_id' => $user->currentTeam->id,
    ]);

    livewire(ListAccounts::class)
        ->assertTableColumnFormattedStateSet('balance', '€1,234.56', record: $account);

    $account = Account::factory()->create([
        'balance' => 3456789,
        'currency' => 'USD',
        'team_id' => $user->currentTeam->id,
    ]);

    livewire(ListAccounts::class)
        ->assertTableColumnFormattedStateSet('balance', '$34,567.89', record: $account);
});

test('iban is properly formatted', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $account = Account::factory()->create([
        'iban' => 'DE89370400440532013000',
        'team_id' => $user->currentTeam->id,
    ]);

    livewire(ListAccounts::class)
        ->assertTableColumnFormattedStateSet('iban', 'DE89 3704 0044 0532 0130 00', record: $account);

    $account = Account::factory()->create([
        'iban' => 'PT50002700000001234567833',
        'team_id' => $user->currentTeam->id,
    ]);

    livewire(ListAccounts::class)
        ->assertTableColumnFormattedStateSet('iban', 'PT50 0027 0000 0001 2345 6783 3', record: $account);
});
