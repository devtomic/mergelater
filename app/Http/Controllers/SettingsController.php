<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function show(): View
    {
        return view('settings');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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
    }
}
