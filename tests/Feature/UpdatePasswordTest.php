<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('password can be updated', function (): void {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(UpdatePasswordForm::class)
        ->set('state', [
            'current_password' => '7Xfss!HoCiMTV',
            'password' => 'N.rxRZ6atC9cL',
            'password_confirmation' => 'N.rxRZ6atC9cL',
        ])
        ->call('updatePassword');

    expect(Hash::check('N.rxRZ6atC9cL', $user->fresh()->password))->toBeTrue();
});

test('current password must be correct', function (): void {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(UpdatePasswordForm::class)
        ->set('state', [
            'current_password' => 'wrong-password',
            'password' => 'N.rxRZ6atC9cL',
            'password_confirmation' => 'N.rxRZ6atC9cL',
        ])
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    expect(Hash::check('7Xfss!HoCiMTV', $user->fresh()->password))->toBeTrue();
});

test('new passwords must match', function (): void {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(UpdatePasswordForm::class)
        ->set('state', [
            'current_password' => 'password',
            'password' => 'N.rxRZ6atC9cL',
            'password_confirmation' => 'khabpFY6M@swq',
        ])
        ->call('updatePassword')
        ->assertHasErrors(['password']);

    expect(Hash::check('7Xfss!HoCiMTV', $user->fresh()->password))->toBeTrue();
});
