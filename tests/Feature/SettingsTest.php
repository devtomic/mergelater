<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to login', function () {
    $response = $this->get('/settings');

    $response->assertRedirect('/login');
});

it('displays settings page for authenticated users', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)->get('/settings');

    $response->assertStatus(200);
    $response->assertSee('Settings');
});

it('updates user settings', function () {
    $user = User::factory()->create([
        'timezone' => 'America/New_York',
        'email_notifications' => true,
        'slack_webhook_url' => null,
    ]);

    $response = $this->actingAs($user)->post('/settings', [
        'timezone' => 'Europe/London',
        'email_notifications' => '0',
        'slack_webhook_url' => 'https://hooks.slack.com/services/xxx',
    ]);

    $response->assertRedirect('/settings');

    $user->refresh();
    expect($user->timezone)->toBe('Europe/London');
    expect($user->email_notifications)->toBeFalse();
    expect($user->slack_webhook_url)->toBe('https://hooks.slack.com/services/xxx');
});

it('validates timezone is valid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/settings', [
        'timezone' => 'Invalid/Timezone',
        'email_notifications' => '1',
    ]);

    $response->assertSessionHasErrors('timezone');
});
