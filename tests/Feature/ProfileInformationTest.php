<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('current profile information is available', function (): void {
    $this->actingAs($user = User::factory()->create());

    $component = Livewire::test(UpdateProfileInformationForm::class);

    expect($component->state['name'])->toEqual($user->name);
    expect($component->state['email'])->toEqual($user->email);

    $component->refresh();
});

test('profile information can be updated', function (): void {
    $this->actingAs($user = User::factory()->create());

    // For unknown reason the verification.verify route is not available
    // at the moment this test runs. Because the route is not relevant for the test,
    // we mock up a dummy route to prevent a Laravel RouteNotFoundException.
    Route::get('/dummy-route')->name('verification.verify');

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state', ['name' => 'Test User', 'email' => 'test@example.com'])
        ->call('updateProfileInformation');

    expect($user->fresh()->name)->toEqual('Test User');
    expect($user->fresh()->email)->toEqual('test@example.com');
});
