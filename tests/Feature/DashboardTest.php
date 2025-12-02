<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to login', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

it('displays dashboard for authenticated users', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Schedule a Merge');
});
