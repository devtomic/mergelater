<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MergeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', fn () => view('home'))->name('home');

// Auth routes (guest)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/auth/github', [AuthController::class, 'redirectToGitHub']);
Route::get('/auth/github/callback', [AuthController::class, 'handleGitHubCallback']);

// Auth routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/onboarding', [OnboardingController::class, 'show']);
    Route::post('/onboarding', [OnboardingController::class, 'store']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::post('/merges/validate', [MergeController::class, 'validate']);
    Route::get('/merges/preview', [MergeController::class, 'preview']);
    Route::post('/merges', [MergeController::class, 'store']);
    Route::delete('/merges/{merge}', [MergeController::class, 'destroy']);

    Route::get('/settings', [SettingsController::class, 'show']);
    Route::post('/settings', [SettingsController::class, 'update']);
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/users/{user}', [AdminController::class, 'showUser']);
    Route::get('/merges', [AdminController::class, 'merges']);
});
