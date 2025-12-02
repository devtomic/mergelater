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
