<?php

declare(strict_types=1);

use Filament\Pages\Auth\Register;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function (): void {
    if (! Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    $response = $this->get('/app/register');

    $response->assertStatus(200);
});

test('registration screen cannot be rendered if support is disabled', function (): void {
    if (Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is enabled.');
    }

    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('new users can register', function (): void {
    if (! Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    livewire(Register::class)
        ->set('data.name', 'Test User')
        ->set('data.email', 'test@example.com')
        ->set('data.password', '7Xfss!HoCiMTV')
        ->set('data.passwordConfirmation', '7Xfss!HoCiMTV')
        ->call('register');

    $this->assertAuthenticated();
});
