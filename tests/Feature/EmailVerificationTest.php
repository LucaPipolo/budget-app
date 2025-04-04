<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;

uses(RefreshDatabase::class);

test('email verification screen can be rendered', function (): void {
    if (! Features::enabled(Features::emailVerification())) {
        $this->markTestSkipped('Email verification not enabled.');
    }

    $user = User::factory()->withPersonalTeam()->unverified()->create();

    $response = $this->actingAs($user)->get('/app/email-verification/prompt');

    $response->assertStatus(200);
});
test('email can be verified', function (): void {
    if (! Features::enabled(Features::emailVerification())) {
        $this->markTestSkipped('Email verification not enabled.');
    }

    Event::fake();

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect('/app/new');
});
test('email can not verified with invalid hash', function (): void {
    if (! Features::enabled(Features::emailVerification())) {
        $this->markTestSkipped('Email verification not enabled.');
    }

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
