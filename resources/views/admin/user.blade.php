<x-layouts.app title="User Details">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <header class="mb-10">
            <div class="flex items-center gap-2 text-sm text-text-muted mb-4">
                <a href="/admin" class="hover:text-text transition-colors">Admin</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="/admin/users" class="hover:text-text transition-colors">Users</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-text">{{ $user->name }}</span>
            </div>
            <h1 class="text-3xl font-bold text-text mb-2">User Details</h1>
            <p class="text-text-muted">View detailed information about this user account.</p>
        </header>

        <div class="card p-6">
            <div class="grid gap-6 md:grid-cols-2">
                {{-- User Profile --}}
                <div class="flex items-center justify-center gap-4">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="" class="size-24 shrink-0 rounded-full ring-2 ring-border">
                    @else
                        <div class="size-24 shrink-0 rounded-full bg-surface-overlay flex items-center justify-center text-text-muted text-3xl font-medium">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold text-text">{{ $user->name }}</h2>
                        <p class="text-sm text-text-muted font-mono">{{ $user->email }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            @if($user->is_admin)
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-terminal/10 text-terminal text-xs font-medium">
                                    Admin
                                </span>
                            @endif
                            @if($user->is_disabled)
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-error/10 text-error text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                    Disabled
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-terminal/10 text-terminal text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-terminal"></span>
                                    Active
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Account Info --}}
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-text-muted">User ID</dt>
                        <dd class="text-text font-mono">{{ $user->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-text-muted">GitHub Username</dt>
                        <dd class="text-text font-mono">{{ $user->github_username ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-text-muted">Timezone</dt>
                        <dd class="text-text">{{ $user->timezone ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-text-muted">Scheduled Merges</dt>
                        <dd class="text-text">{{ $user->scheduled_merges_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-text-muted">Joined</dt>
                        <dd class="text-text">{{ $user->created_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-text-muted">Last Updated</dt>
                        <dd class="text-text">{{ $user->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- User's Merges --}}
        <div class="mt-6">
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border-subtle">
                    <h3 class="text-lg font-semibold text-text">Scheduled Merges</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-surface-raised">
                        <tr class="text-left text-sm text-text-muted">
                            <th class="px-6 py-4 font-medium">Pull Request</th>
                            <th class="px-6 py-4 font-medium">Method</th>
                            <th class="px-6 py-4 font-medium">Scheduled</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse($user->scheduledMerges()->latest()->limit(10)->get() as $merge)
                            <tr class="hover:bg-surface-raised/50 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ $merge->pull_request_url }}" target="_blank" class="text-text hover:text-terminal transition-colors font-mono text-sm">
                                        {{ $merge->owner }}/{{ $merge->repo }}#{{ $merge->pull_request_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-muted capitalize">{{ $merge->merge_method }}</td>
                                <td class="px-6 py-4 text-sm text-text-muted">{{ $merge->scheduled_at->format('M j, Y g:i A') }}</td>
                                <td class="px-6 py-4">
                                    @if($merge->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-warning/10 text-warning text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-warning"></span>
                                            Pending
                                        </span>
                                    @elseif($merge->status === 'processing')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-info/10 text-info text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-info"></span>
                                            Processing
                                        </span>
                                    @elseif($merge->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-terminal/10 text-terminal text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-terminal"></span>
                                            Completed
                                        </span>
                                    @elseif($merge->status === 'failed')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-error/10 text-error text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-text-subtle">
                                    No scheduled merges yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
