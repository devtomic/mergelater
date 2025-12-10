<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

it('displays the login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Continue with GitHub');
});

it('redirects to GitHub OAuth', function () {
    $response = $this->get('/auth/github');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('github.com');
});

it('creates a new user from GitHub callback', function () {
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn(12345);
    $socialiteUser->shouldReceive('getName')->andReturn('Test User');
    $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://github.com/avatar.jpg');
    $socialiteUser->token = 'github-token-123';

    Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

    $response = $this->get('/auth/github/callback');

    $response->assertRedirect('/onboarding');

    $this->assertDatabaseHas('users', [
        'github_id' => 12345,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->assertAuthenticated();
});

it('redirects existing user with completed onboarding to dashboard', function () {
    $existingUser = User::factory()->create([
        'github_id' => 12345,
        'onboarding_completed_at' => now(),
    ]);

    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn(12345);
    $socialiteUser->shouldReceive('getName')->andReturn('Test User');
    $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://github.com/avatar.jpg');
    $socialiteUser->token = 'new-token';

    Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

    $response = $this->get('/auth/github/callback');

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($existingUser);
});

it('uses GitHub username when name is null', function () {
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn(98765);
    $socialiteUser->shouldReceive('getName')->andReturn(null);
    $socialiteUser->shouldReceive('getNickname')->andReturn('githubusername');
    $socialiteUser->shouldReceive('getEmail')->andReturn('noname@example.com');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://github.com/avatar.jpg');
    $socialiteUser->token = 'github-token-456';

    Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

    $response = $this->get('/auth/github/callback');

    $response->assertRedirect('/onboarding');

    $this->assertDatabaseHas('users', [
        'github_id' => 98765,
        'name' => 'githubusername',
        'email' => 'noname@example.com',
    ]);

    $this->assertAuthenticated();
});

it('logs out the user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});
