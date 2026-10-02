<?php

use Illuminate\Support\Facades\DB;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('registration requires agreement to the terms and privacy policy', function () {
    $message = 'You must agree to the Terms of Service and Privacy Policy.';

    $response = $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors('agree');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    $this->get('/register')->assertOk()->assertSee($message);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'agree' => 'on',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->get(route('dashboard'))->assertOk();

    $userId = auth()->id();
    $workspaceId = DB::table('users')->where('id', $userId)->value('current_workspace_id');

    expect($workspaceId)->not->toBeNull();

    $this->assertDatabaseHas('users', [
        'id' => $userId,
        'current_workspace_id' => $workspaceId,
    ]);

    $this->assertDatabaseHas('workspaces', [
        'id' => $workspaceId,
        'user_id' => $userId,
    ]);

    $this->assertDatabaseHas('workspace_users', [
        'workspace_id' => $workspaceId,
        'user_id' => $userId,
        'role' => 'owner',
    ]);

    expect(auth()->user()->hasVerifiedEmail())->toBeFalse();
});
