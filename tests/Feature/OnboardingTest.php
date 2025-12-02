<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires authentication to access onboarding', function () {
    $response = $this->get('/onboarding');

    $response->assertRedirect('/login');
});

it('displays the timezone selection form', function () {
    $user = User::factory()->create(['onboarding_completed_at' => null]);

    $response = $this->actingAs($user)->get('/onboarding');

    $response->assertStatus(200);
    $response->assertSee('Select your timezone');
});

it('saves timezone and marks onboarding complete', function () {
    $user = User::factory()->create(['onboarding_completed_at' => null]);

    $response = $this->actingAs($user)->post('/onboarding', [
        'timezone' => 'America/New_York',
    ]);

    $response->assertRedirect('/dashboard');

    $user->refresh();
    expect($user->timezone)->toBe('America/New_York');
    expect($user->onboarding_completed_at)->not->toBeNull();
});

it('redirects users who completed onboarding to dashboard', function () {
    $user = User::factory()->create(['onboarding_completed_at' => now()]);

    $response = $this->actingAs($user)->get('/onboarding');

    $response->assertRedirect('/dashboard');
});
