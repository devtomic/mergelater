<x-layouts.app title="Admin Dashboard">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <header class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-lg bg-terminal/10 border border-terminal/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-text">Admin Dashboard</h1>
            </div>
            <p class="text-text-muted">System overview and user management.</p>
        </header>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Total Users --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-text-muted">Total Users</span>
                    <div class="w-8 h-8 rounded-lg bg-terminal/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-text font-mono">{{ \App\Models\User::count() }}</div>
                <div class="text-xs text-text-subtle mt-1">registered accounts</div>
            </div>

            {{-- Pending Merges --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-text-muted">Pending Merges</span>
                    <div class="w-8 h-8 rounded-lg bg-warning/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-text font-mono">{{ \App\Models\ScheduledMerge::where('status', 'pending')->count() }}</div>
                <div class="text-xs text-text-subtle mt-1">scheduled merges</div>
            </div>

            {{-- Completed Merges --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-text-muted">Completed</span>
                    <div class="w-8 h-8 rounded-lg bg-terminal/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-text font-mono">{{ \App\Models\ScheduledMerge::where('status', 'completed')->count() }}</div>
                <div class="text-xs text-text-subtle mt-1">successful merges</div>
            </div>

            {{-- Failed Merges --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-text-muted">Failed</span>
                    <div class="w-8 h-8 rounded-lg bg-error/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-text font-mono">{{ \App\Models\ScheduledMerge::where('status', 'failed')->count() }}</div>
                <div class="text-xs text-text-subtle mt-1">failed merges</div>
            </div>
        </div>

        {{-- Admin Navigation --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <a href="/admin/users" class="card p-6 group hover:border-terminal/30 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-overlay flex items-center justify-center group-hover:bg-terminal/10 transition-colors">
                        <svg class="w-6 h-6 text-text-muted group-hover:text-terminal transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text group-hover:text-terminal transition-colors">Manage Users</h3>
                        <p class="text-sm text-text-muted">View and manage user accounts</p>
                    </div>
                </div>
            </a>

            <a href="/admin/merges" class="card p-6 group hover:border-terminal/30 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-overlay flex items-center justify-center group-hover:bg-terminal/10 transition-colors">
                        <svg class="w-6 h-6 text-text-muted group-hover:text-terminal transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text group-hover:text-terminal transition-colors">All Merges</h3>
                        <p class="text-sm text-text-muted">View all scheduled merges</p>
                    </div>
                </div>
            </a>

            <div class="card p-6 opacity-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-overlay flex items-center justify-center">
                        <svg class="w-6 h-6 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text">System Settings</h3>
                        <p class="text-sm text-text-muted">Coming soon</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <section class="card">
            <div class="px-6 py-4 border-b border-border-subtle">
                <h2 class="font-semibold text-text">Recent Merges</h2>
            </div>
            <div class="divide-y divide-border-subtle">
                @forelse(\App\Models\ScheduledMerge::with('user')->latest()->take(10)->get() as $merge)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            @if($merge->user->avatar_url)
                                <img src="{{ $merge->user->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 rounded-full bg-surface-overlay"></div>
                            @endif
                            <div>
                                <div class="font-mono text-sm text-text">{{ $merge->owner }}/{{ $merge->repo }}#{{ $merge->pull_number }}</div>
                                <div class="text-xs text-text-muted">{{ $merge->user->name }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-text-subtle">{{ $merge->scheduled_at->diffForHumans() }}</span>
                            <span class="status-{{ $merge->status }} text-xs font-medium uppercase tracking-wide">{{ $merge->status }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="text-text-subtle">No merges scheduled yet.</div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
