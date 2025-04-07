<?php

declare(strict_types=1);

use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Models\Account;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

test('can sort accounts by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory()->count(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $expectedAsc = $accounts->sortBy('name')->pluck('name')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('name', 'asc')
        ->assertSeeInOrder($expectedAsc);

    $expectedDesc = $accounts->sortByDesc('name')->pluck('name')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('name', 'desc')
        ->assertSeeInOrder($expectedDesc);
});

test('can sort accounts by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory()->count(5)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $expectedAsc = $accounts->sortBy('balance')->pluck('balance')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('balance', 'asc')
        ->assertSeeInOrder($expectedAsc);

    $expectedDesc = $accounts->sortByDesc('balance')->pluck('balance')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('balance', 'desc')
        ->assertSeeInOrder($expectedDesc);
});

test('can sort accounts by created at date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory(5)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'created_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    $expectedAsc = $accounts->sortBy('created_at')->pluck('id')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('created_at', 'asc')
        ->assertSeeInOrder($expectedAsc);

    $expectedDesc = $accounts->sortByDesc('created_at')->pluck('id')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('created_at', 'desc')
        ->assertSeeInOrder($expectedDesc);
});

test('can sort accounts by updated at date', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    $this->actingAs($user);
    Filament::setTenant($user->currentTeam);

    $accounts = Account::factory(5)
        ->sequence(function ($sequence) use ($user) {
            return [
                'team_id' => $user->currentTeam->id,
                'updated_at' => now()->subDays($sequence->index),
            ];
        })
        ->create();

    $expectedAsc = $accounts->sortBy('updated_at')->pluck('id')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('updated_at', 'asc')
        ->assertSeeInOrder($expectedAsc);

    $expectedDesc = $accounts->sortByDesc('updated_at')->pluck('id')->toArray();

    livewire(ListAccounts::class)
        ->sortTable('updated_at', 'desc')
        ->assertSeeInOrder($expectedDesc);
});
//
// test('can sort accounts by update date', function (): void {
//    $user = User::factory()->create();
//    $team = Team::factory()->create(['user_id' => $user->id]);
//
//    $accounts = collect();
//    for ($i = 0; $i < 5; $i++) {
//        $accounts->push(Account::factory()->create([
//            'team_id' => $team->id,
//            'updated_at' => CarbonImmutable::now()->subDays(rand(1, 365)),
//        ]));
//    }
//
//    $this->actingAs($user);
//    Filament::setTenant($team);
//
//    $expectedAsc = $accounts->sortBy('updated_at')->pluck('id')->toArray();
//    $expectedDesc = $accounts->sortByDesc('updated_at')->pluck('id')->toArray();
//
//    livewire(ListAccounts::class)
//        ->sortTable('updated_at')
//        ->assertSeeInOrder($expectedAsc)
//        ->sortTable('updated_at')
//        ->assertSeeInOrder($expectedDesc);
// });
