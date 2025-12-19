<?php

namespace App\Http\Controllers;

use App\Exceptions\GitHubAccessDeniedException;
use App\Models\ScheduledMerge;
use App\Services\GitHubService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MergeController extends Controller
{
    public function validate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'github_pr_url' => 'required|string',
            'merge_method' => 'required|in:merge,squash,rebase',
            'scheduled_at' => 'required|date',
        ]);

        $parsed = ScheduledMerge::parseGitHubUrl($validated['github_pr_url']);

        if (! $parsed) {
            return back()->withErrors(['github_pr_url' => 'Invalid GitHub PR URL'])->withInput();
        }

        $githubService = new GitHubService(auth()->user()->github_token);

        try {
            $prData = $githubService->getPullRequest($parsed['owner'], $parsed['repo'], $parsed['pull_number']);
        } catch (GitHubAccessDeniedException $e) {
            return back()->withErrors(['github_pr_url' => 'You don\'t have access to this repository.'])->withInput();
        }

        if ($prData === null) {
            return back()->withErrors(['github_pr_url' => 'PR not found. Please check the URL and try again.'])->withInput();
        }

        if ($prData['state'] === 'closed' && ($prData['merged'] ?? false)) {
            return back()->withErrors(['github_pr_url' => 'This PR has already been merged.'])->withInput();
        }

        if ($prData['state'] === 'closed') {
            return back()->withErrors(['github_pr_url' => 'This PR has been closed without merging.'])->withInput();
        }

        $userTimezone = auth()->user()->timezone;
        $scheduledAt = Carbon::parse($validated['scheduled_at'], $userTimezone)->utc();

        session()->put('pending_merge', [
            'github_pr_url' => $parsed['url'],
            'owner' => $parsed['owner'],
            'repo' => $parsed['repo'],
            'pull_number' => $parsed['pull_number'],
            'merge_method' => $validated['merge_method'],
            'scheduled_at' => $scheduledAt->toDateTimeString(),
            'pr_data' => $prData,
        ]);

        return redirect('/merges/preview');
    }

    public function preview(): RedirectResponse|View
    {
        $pendingMerge = session('pending_merge');

        if (! $pendingMerge) {
            return redirect('/dashboard');
        }

        return view('merges.preview', [
            'pendingMerge' => $pendingMerge,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pendingMerge = session('pending_merge');

        if ($pendingMerge) {
            auth()->user()->scheduledMerges()->create([
                'github_pr_url' => $pendingMerge['github_pr_url'],
                'owner' => $pendingMerge['owner'],
                'repo' => $pendingMerge['repo'],
                'pull_number' => $pendingMerge['pull_number'],
                'merge_method' => $pendingMerge['merge_method'],
                'scheduled_at' => $pendingMerge['scheduled_at'],
                'status' => 'pending',
            ]);

            session()->forget('pending_merge');

            return redirect('/dashboard');
        }

        $validated = $request->validate([
            'github_pr_url' => 'required|string',
            'merge_method' => 'required|in:merge,squash,rebase',
            'scheduled_at' => 'required|date',
        ]);

        $userTimezone = auth()->user()->timezone;
        $scheduledAt = Carbon::parse($validated['scheduled_at'], $userTimezone)->utc();

        if ($scheduledAt->isPast()) {
            return back()->withErrors(['scheduled_at' => 'The scheduled time must be in the future.'])->withInput();
        }

        $parsed = ScheduledMerge::parseGitHubUrl($validated['github_pr_url']);

        if (! $parsed) {
            return back()->withErrors(['github_pr_url' => 'Invalid GitHub PR URL']);
        }

        auth()->user()->scheduledMerges()->create([
            'github_pr_url' => $parsed['url'],
            'owner' => $parsed['owner'],
            'repo' => $parsed['repo'],
            'pull_number' => $parsed['pull_number'],
            'merge_method' => $validated['merge_method'],
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
        ]);

        return redirect('/dashboard');
    }

    public function destroy(ScheduledMerge $merge): RedirectResponse
    {
        if ($merge->user_id !== auth()->id()) {
            abort(403);
        }

        $merge->delete();

        return redirect('/dashboard');
    }
}
