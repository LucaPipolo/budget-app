<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('confirm password screen can be rendered', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);

    $response = Livewire::test(TwoFactorAuthenticationForm::class);

    $response->call('startConfirmingPassword', true);
    $response->assertSet('confirmingPassword', true);
});

test('password can be confirmed', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);

    Livewire::test(TwoFactorAuthenticationForm::class)
        ->call('startConfirmingPassword', true)
        ->set('confirmablePassword', '7Xfss!HoCiMTV')
        ->call('confirmPassword')
        ->assertHasNoErrors('confirmable_password')
        ->assertSet('confirmingPassword', false);
});

test('password is not confirmed with invalid password', function (): void {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);

    Livewire::test(TwoFactorAuthenticationForm::class)
        ->call('startConfirmingPassword', true)
        ->set('confirmablePassword', 'wrong-password')
        ->call('confirmPassword')
        ->assertHasErrors('confirmable_password')
        ->assertSet('confirmingPassword', true);
});
