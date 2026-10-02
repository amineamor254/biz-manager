<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->from('/forgot-password')
        ->post('/forgot-password', ['email' => $user->email])
        ->assertRedirect('/forgot-password')
        ->assertSessionHas('status', 'If an account exists for that email address, a password reset link will be sent shortly.')
        ->assertSessionHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('forgot password rejects an invalid email format', function () {
    $this->from('/forgot-password')
        ->post('/forgot-password', ['email' => 'not-an-email'])
        ->assertRedirect('/forgot-password')
        ->assertSessionHasErrors('email');
});

test('forgot password does not reveal whether an email exists', function () {
    $message = 'If an account exists for that email address, a password reset link will be sent shortly.';

    $this->from('/forgot-password')
        ->post('/forgot-password', ['email' => 'unknown@example.com'])
        ->assertRedirect('/forgot-password')
        ->assertSessionHas('status', $message)
        ->assertSessionHasNoErrors();
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create(['password' => 'old-password']);

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue()
            ->and(Hash::check('old-password', $user->fresh()->password))->toBeFalse()
            ->and(DB::table('password_reset_tokens')->where('email', $user->email)->exists())->toBeFalse();

        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'another-password',
            'password_confirmation' => 'another-password',
        ])->assertSessionHasErrors('email');

        $this->post('/login', ['email' => $user->email, 'password' => 'old-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'new-password'])
            ->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);

        return true;
    });
});

test('password reset rejects an invalid token', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    $this->from('/reset-password')->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirect('/reset-password')->assertSessionHasErrors('email');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('password reset rejects an expired token', function () {
    Notification::fake();

    $user = User::factory()->create(['password' => 'old-password']);

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->update(['created_at' => now()->subMinutes(61)]);

        $this->from('/reset-password/'.$notification->token)->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect('/reset-password/'.$notification->token)->assertSessionHasErrors('email');

        return true;
    });

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});
