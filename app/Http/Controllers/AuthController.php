<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function redirectToGitHub(): RedirectResponse
    {
        return Socialite::driver('github')->scopes(['repo'])->redirect();
    }

    public function handleGitHubCallback(): RedirectResponse
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
            ['github_id' => $githubUser->getId()],
            [
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
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
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
