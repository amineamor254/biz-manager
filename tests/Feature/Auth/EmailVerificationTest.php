<?php

use App\Models\User;
use App\Services\WorkspaceService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
});

test('new registrations receive a verification email and remain unverified', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Unverified User',
        'email' => 'unverified@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'agree' => 'on',
    ]);

    $user = User::query()->where('email', 'unverified@example.com')->firstOrFail();

    $response->assertRedirect(route('dashboard', absolute: false));
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('existing unverified users can log in and reach the dashboard', function () {
    $user = User::factory()->unverified()->create();
    $workspace = app(WorkspaceService::class)->createWorkspace($user, [
        'name' => $user->name . "'s Business",
    ]);
    $user->update(['current_workspace_id' => $workspace->id]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    $this->get(route('dashboard'))->assertOk();
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl)->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('expired verification links are rejected', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->subMinute(),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)->get($verificationUrl)->assertForbidden();
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verified users are redirected away from the verification prompt', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)->get(route('verification.notice'))
        ->assertRedirect(route('dashboard', absolute: false));
});

test('unverified users can request another verification email', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->from('/verify-email')->post(route('verification.send'))
        ->assertRedirect('/verify-email')
        ->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('already verified users do not receive another verification email', function () {
    Notification::fake();
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)->post(route('verification.send'))
        ->assertRedirect(route('dashboard', absolute: false));

    Notification::assertNotSentTo($user, VerifyEmail::class);
});
