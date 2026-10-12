<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('portal.home', absolute: false));
});

test('login returns a user to their intended intranet page', function () {
    $user = User::factory()->create();

    $this->get('/reports')->assertRedirect('/login');

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/reports');
});

test('inertia login performs a full visit when entering the non-inertia portal', function () {
    $user = User::factory()->create();

    $this->withHeaders(['X-Inertia' => 'true', 'X-Requested-With' => 'XMLHttpRequest'])
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertStatus(409)
        ->assertHeader('X-Inertia-Location', route('portal.home'));

    $this->assertAuthenticatedAs($user);
});

test('legacy dashboard links lead to the intranet instead of the Breeze placeholder', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertRedirect(route('portal.home'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('users can logout and be redirected to admin login to switch account', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout', [
        'redirect' => '/admin/login',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/admin/login');
});
