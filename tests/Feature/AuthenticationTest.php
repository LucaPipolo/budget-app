<?php

declare(strict_types=1);
use App\Models\User;
use Filament\Pages\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('login screen can be rendered', function (): void {
    $response = $this->get('/app/login');

    $response->assertStatus(200);
});
test('users can authenticate using the login screen', function (): void {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = livewire(Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate');

    expect(auth()->check())->toBeTrue();

    $response->assertRedirect('/app/new');
});
test('users can not authenticate with invalid password', function (): void {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
