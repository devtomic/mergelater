<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return redirect('/login');
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

Route::get('/onboarding', function () {
    if (Auth::user()->hasCompletedOnboarding()) {
        return redirect('/dashboard');
    }

    return view('onboarding');
})->middleware('auth');

Route::post('/onboarding', function () {
    $validated = request()->validate([
        'timezone' => 'required|string|timezone',
    ]);

    Auth::user()->update([
        'timezone' => $validated['timezone'],
        'onboarding_completed_at' => now(),
    ]);

    return redirect('/dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/settings', function () {
    return view('settings');
})->middleware('auth');

Route::post('/settings', function () {
    $validated = request()->validate([
        'timezone' => 'required|string|timezone',
        'email_notifications' => 'required|boolean',
        'slack_webhook_url' => 'nullable|url',
    ]);

    auth()->user()->update([
        'timezone' => $validated['timezone'],
        'email_notifications' => $validated['email_notifications'],
        'slack_webhook_url' => $validated['slack_webhook_url'],
    ]);

    return redirect('/settings');
})->middleware('auth');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'admin']);

Route::get('/admin/users', function () {
    return view('admin.users', [
        'users' => \App\Models\User::latest()->paginate(20),
    ]);
})->middleware(['auth', 'admin']);

Route::get('/admin/merges', function () {
    return view('admin.merges', [
        'merges' => \App\Models\ScheduledMerge::with('user')->latest()->paginate(20),
    ]);
})->middleware(['auth', 'admin']);
