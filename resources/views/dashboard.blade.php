<x-layouts.app title="Dashboard">
    <div class="max-w-6xl mx-auto px-6 py-10">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-2xl font-bold text-text">Dashboard</h1>
                <p class="text-text-muted mt-1">Manage your scheduled merges</p>
            </div>
            <div class="text-sm text-text-subtle font-mono">
                {{ now()->setTimezone(auth()->user()->timezone)->format('M j, Y · g:i A') }}
                <span class="text-text-muted">({{ auth()->user()->timezone }})</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Schedule form --}}
            <div class="lg:col-span-1">
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-text mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Schedule a Merge
                    </h2>

                    <form method="POST" action="/merges/validate" class="space-y-5" x-data="{ loading: false }" @submit="loading = true">
                        @csrf

                        @if ($errors->any())
                            <div class="p-3 rounded-lg bg-error/10 border border-error/20">
                                <p class="text-sm text-error">Please correct the errors below.</p>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label for="github_pr_url" class="block text-sm font-medium text-text">Pull Request URL</label>
                            <input
                                type="url"
                                name="github_pr_url"
                                id="github_pr_url"
                                class="input-field"
                                placeholder="https://github.com/owner/repo/pull/123"
                                required
                            >
                            @error('github_pr_url')
                                <p class="text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="merge_method" class="block text-sm font-medium text-text">Merge Method</label>
                            <select name="merge_method" id="merge_method" class="input-field" required>
                                <option value="squash">Squash and merge</option>
                                <option value="merge">Create a merge commit</option>
                                <option value="rebase">Rebase and merge</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="scheduled_at" class="block text-sm font-medium text-text">Schedule For</label>
                            <div class="flex gap-2">
                                <input
                                    type="datetime-local"
                                    name="scheduled_at"
                                    id="scheduled_at"
                                    class="input-field flex-1 min-w-0"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="document.getElementById('scheduled_at').value = new Date(Date.now() + 60000).toLocaleString('sv').slice(0, 16).replace(' ', 'T')"
                                    class="px-3 py-2 text-sm font-medium bg-surface-overlay hover:bg-surface-raised border border-border rounded-lg text-text-muted hover:text-text transition-colors"
                                    title="Set to 1 minute from now"
                                >
                                    Now
                                </button>
                            </div>
                            @error('scheduled_at')
                                <p class="text-sm text-error">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-text-subtle">Times are in your local timezone ({{ auth()->user()->timezone }})</p>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn-primary w-full" :disabled="loading" :class="{ 'opacity-75 cursor-not-allowed': loading }">
                                <template x-if="!loading">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Schedule Merge
                                    </span>
                                </template>
                                <template x-if="loading">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Validating PR...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Scheduled merges list --}}
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="p-6 border-b border-border-subtle">
                        <h2 class="text-lg font-semibold text-text flex items-center gap-2">
                            <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Scheduled Merges
                        </h2>
                    </div>

                    @if(auth()->user()->scheduledMerges()->count() > 0)
                        <div class="divide-y divide-border-subtle">
                            @foreach(auth()->user()->scheduledMerges()->orderBy('scheduled_at')->get() as $merge)
                                <div class="p-6 flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4 min-w-0">
                                        {{-- Status indicator --}}
                                        <div class="mt-1">
                                            @if($merge->isPending())
                                                <div class="w-3 h-3 rounded-full bg-text-muted"></div>
                                            @elseif($merge->status === 'processing')
                                                <div class="w-3 h-3 rounded-full bg-processing animate-pulse"></div>
                                            @elseif($merge->isCompleted())
                                                <div class="w-3 h-3 rounded-full bg-terminal"></div>
                                            @else
                                                <div class="w-3 h-3 rounded-full bg-error"></div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <a href="{{ $merge->github_pr_url }}" target="_blank" class="font-mono text-sm text-text hover:text-terminal transition-colors truncate">
                                                    {{ $merge->owner }}/{{ $merge->repo }}#{{ $merge->pull_number }}
                                                </a>
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-surface-overlay text-text-muted">
                                                    {{ $merge->merge_method }}
                                                </span>
                                            </div>

                                            <div class="text-sm text-text-muted">
                                                @if($merge->isPending())
                                                    Scheduled for {{ $merge->scheduled_at->setTimezone(auth()->user()->timezone)->format('M j, g:i A') }}
                                                @elseif($merge->status === 'processing')
                                                    <span class="status-processing">Processing...</span>
                                                @elseif($merge->isCompleted())
                                                    <span class="status-completed">Merged {{ $merge->merged_at->diffForHumans() }}</span>
                                                @else
                                                    <span class="status-failed">Failed: {{ $merge->error_message }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($merge->isPending())
                                        <form method="POST" action="/merges/{{ $merge->id }}" class="shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-text-subtle hover:text-error transition-colors" title="Cancel merge">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-surface-raised border border-border mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-text-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-text-muted mb-1">No scheduled merges yet</p>
                            <p class="text-sm text-text-subtle">Schedule your first merge using the form</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
