<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(): RedirectResponse|View
    {
        if (Auth::user()->hasCompletedOnboarding()) {
            return redirect('/dashboard');
        }

        return view('onboarding');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => 'required|string|timezone',
        ]);

        Auth::user()->update([
            'timezone' => $validated['timezone'],
            'onboarding_completed_at' => now(),
        ]);

        return redirect('/dashboard');
    }
}
