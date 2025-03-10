<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword as ResetPasswordPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('reset password link screen can be rendered', function (): void {
    if (! Features::enabled(Features::resetPasswords())) {
        $this->markTestSkipped('Password updates are not enabled.');
    }

    $response = $this->get('/app/password-reset/request');

    $response->assertStatus(200);
});

test('reset password link can be requested', function (): void {
    if (! Features::enabled(Features::resetPasswords())) {
        $this->markTestSkipped('Password updates are not enabled.');
    }

    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->set('data.email', $user->email)
        ->call('request');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function (): void {
    if (! Features::enabled(Features::resetPasswords())) {
        $this->markTestSkipped('Password updates are not enabled.');
    }

    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->set('data.email', $user->email)
        ->call('request');

    Notification::assertSentTo($user, ResetPassword::class, function (object $notification) use ($user) {
        $signedUrl = URL::temporarySignedRoute(
            'filament.app.auth.password-reset.reset',
            now()->addMinutes(60),
            ['token' => $notification->token, 'email' => $user->email]
        );

        $response = $this->get($signedUrl);
        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function (): void {
    if (! Features::enabled(Features::resetPasswords())) {
        $this->markTestSkipped('Password updates are not enabled.');
    }

    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->set('data.email', $user->email)
        ->call('request');

    Notification::assertSentTo($user, ResetPassword::class, function (object $notification) {
        $response = livewire(ResetPasswordPage::class)
            ->set('password', 'my-new-password')
            ->set('passwordConfirmation', 'my-new-password')
            ->call('resetPassword');

        $response->assertHasNoErrors();

        return true;
    });
});
