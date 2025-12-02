<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/github', function () {
    return Socialite::driver('github')->scopes(['repo'])->redirect();
});

Route::get('/auth/github/callback', function () {
    $githubUser = Socialite::driver('github')->user();

    $user = User::updateOrCreate(
        ['github_id' => $githubUser->getId()],
        [
            'name' => $githubUser->getName(),
            'email' => $githubUser->getEmail(),
            'github_token' => $githubUser->token,
            'avatar_url' => $githubUser->getAvatar(),
        ]
    );

    Auth::login($user);

    if ($user->hasCompletedOnboarding()) {
        return redirect('/dashboard');
    }

    return redirect('/onboarding');
});

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
});
