<?php

namespace App\Http\Controllers;

use App\Models\ScheduledMerge;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    public function users(): View
    {
        return view('admin.users', [
            'users' => User::latest()->paginate(20),
        ]);
    }

    public function showUser(User $user): View
    {
        return view('admin.user', [
            'user' => $user->loadCount('scheduledMerges'),
        ]);
    }

    public function merges(): View
    {
        return view('admin.merges', [
            'merges' => ScheduledMerge::with('user')->latest()->paginate(20),
        ]);
    }
}
