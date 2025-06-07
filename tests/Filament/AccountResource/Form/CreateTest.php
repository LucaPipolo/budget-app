<?php

declare(strict_types=1);

use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

test('has a form with all needed fields', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    livewire(CreateAccount::class)
        ->assertFormExists()
        ->assertFormFieldExists('logo_path')
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('type')
        ->assertFormFieldExists('balance')
        ->assertFormFieldExists('currency')
        ->assertFormFieldExists('iban')
        ->assertFormFieldExists('swift');
});

test('can validate required fields', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    livewire(CreateAccount::class)
        ->fillForm([
            'name' => null,
            'type' => null,
            'currency' => null,
        ])
        ->call('create')
        ->assertHasErrors(['data.name' => 'required'])
        ->assertHasErrors(['data.type' => 'required'])
        ->assertHasErrors(['data.currency' => 'required']);
});

test('can validate fields', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    livewire(CreateAccount::class)
        ->fillForm(['name' => 'Aa'])
        ->call('create')
        ->assertHasErrors(['data.name' => 'min:3']);

    livewire(CreateAccount::class)
        ->fillForm(['name' => str_repeat('A', 256)])
        ->call('create')
        ->assertHasErrors(['data.name' => 'max:255']);
});

test('can create if valid input', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    livewire(CreateAccount::class)
        ->fillForm([
            'name' => 'BBVA',
            'type' => 'bank',
            'balance' => '1300',
            'currency' => 'EUR',
            'iban' => 'NL51ABNA3115404417',
            'swift' => 'GIHQPS8CXXX',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('accounts', [
        'name' => 'BBVA',
        'balance' => 1300,
        'team_id' => $user->currentTeam->id,
    ]);
});
