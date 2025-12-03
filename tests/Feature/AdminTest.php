<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to login from admin', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/login');
});

it('denies non-admin users access to admin', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertStatus(403);
});

it('allows admin users access to admin dashboard', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertStatus(200);
    $response->assertSee('Admin Dashboard');
});

it('allows admin users access to users list', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertStatus(200);
    $response->assertSee('Manage Users');
});

it('allows admin users access to merges list', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin/merges');

    $response->assertStatus(200);
    $response->assertSee('All Merges');
});

it('does not show admin link to non-admin users', function () {
    $user = User::factory()->create([
        'is_admin' => false,
        'timezone' => 'UTC',
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertDontSee('href="/admin"', escape: false);
});

it('shows admin link to admin users', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'timezone' => 'UTC',
    ]);

    $response = $this->actingAs($admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('href="/admin"', escape: false);
});

it('allows admin users to view user details', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'testuser@example.com',
    ]);

    $response = $this->actingAs($admin)->get("/admin/users/{$user->id}");

    $response->assertStatus(200);
    $response->assertSee('Test User');
    $response->assertSee('testuser@example.com');
});
