<x-layouts.app title="Confirm Merge">
    <div class="max-w-2xl mx-auto px-6 py-10">
        {{-- Header --}}
        <div class="mb-8">
            <a href="/dashboard" class="inline-flex items-center gap-2 text-sm text-text-muted hover:text-text transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-text">Confirm Scheduled Merge</h1>
            <p class="text-text-muted mt-1">Review the PR details below before scheduling</p>
        </div>

        <div class="card overflow-hidden">
            {{-- PR Header --}}
            <div class="p-6 border-b border-border-subtle bg-surface-raised/50">
                <div class="flex items-start gap-4">
                    @if(isset($pendingMerge['pr_data']['user']['avatar_url']))
                        <img src="{{ $pendingMerge['pr_data']['user']['avatar_url'] }}" alt="" class="w-12 h-12 rounded-full ring-2 ring-border">
                    @else
                        <div class="w-12 h-12 rounded-full bg-surface-overlay flex items-center justify-center">
                            <svg class="w-6 h-6 text-text-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-semibold text-text truncate">{{ $pendingMerge['pr_data']['title'] }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <a href="{{ $pendingMerge['github_pr_url'] }}" target="_blank" class="font-mono text-sm text-terminal hover:underline">
                                {{ $pendingMerge['owner'] }}/{{ $pendingMerge['repo'] }}#{{ $pendingMerge['pull_number'] }}
                            </a>
                            <span class="text-text-subtle">&bull;</span>
                            <span class="text-sm text-text-muted">by {{ $pendingMerge['pr_data']['user']['login'] }}</span>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-terminal/10 text-terminal border border-terminal/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-terminal"></span>
                            Open
                        </span>
                    </div>
                </div>
            </div>

            {{-- PR Details --}}
            <div class="p-6 space-y-6">
                {{-- Branch Flow --}}
                <div class="flex items-center gap-3 p-4 rounded-lg bg-surface-raised border border-border-subtle">
                    <div class="flex-1 text-right">
                        <p class="text-xs text-text-subtle uppercase tracking-wide mb-1">From</p>
                        <p class="font-mono text-sm text-text truncate">{{ $pendingMerge['pr_data']['head']['ref'] }}</p>
                    </div>
                    <div class="shrink-0">
                        <svg class="w-6 h-6 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-text-subtle uppercase tracking-wide mb-1">Into</p>
                        <p class="font-mono text-sm text-text truncate">{{ $pendingMerge['pr_data']['base']['ref'] }}</p>
                    </div>
                </div>

                {{-- Merge Settings --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg bg-surface-raised border border-border-subtle">
                        <p class="text-xs text-text-subtle uppercase tracking-wide mb-1">Merge Method</p>
                        <p class="text-sm font-medium text-text capitalize">{{ str_replace('_', ' ', $pendingMerge['merge_method']) }}</p>
                    </div>
                    <div class="p-4 rounded-lg bg-surface-raised border border-border-subtle">
                        <p class="text-xs text-text-subtle uppercase tracking-wide mb-1">Scheduled For</p>
                        <p class="text-sm font-medium text-text">{{ \Carbon\Carbon::parse($pendingMerge['scheduled_at'])->setTimezone(auth()->user()->timezone)->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="p-6 border-t border-border-subtle bg-surface-raised/30">
                <div class="flex gap-3">
                    <form method="POST" action="/merges" class="flex-1" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        <button type="submit" class="btn-primary w-full" :disabled="loading" :class="{ 'opacity-75 cursor-not-allowed': loading }">
                            <template x-if="!loading">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Confirm &amp; Schedule
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Scheduling...
                                </span>
                            </template>
                        </button>
                    </form>
                    <a href="/dashboard" class="btn-secondary px-6 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
