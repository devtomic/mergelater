<x-layouts.app title="All Merges">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <header class="mb-10">
            <div class="flex items-center gap-2 text-sm text-text-muted mb-4">
                <a href="/admin" class="hover:text-text transition-colors">Admin</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-text">Merges</span>
            </div>
            <h1 class="text-3xl font-bold text-text mb-2">All Merges</h1>
            <p class="text-text-muted">View all scheduled merges across all users.</p>
        </header>

        <div class="card overflow-hidden">
            <table class="w-full">
                <thead class="bg-surface-raised">
                    <tr class="text-left text-sm text-text-muted">
                        <th class="px-6 py-4 font-medium">Pull Request</th>
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Method</th>
                        <th class="px-6 py-4 font-medium">Scheduled</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse($merges as $merge)
                        <tr class="hover:bg-surface-raised/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-mono text-sm text-text">{{ $merge->owner }}/{{ $merge->repo }}#{{ $merge->pull_number }}</div>
                                <a href="{{ $merge->github_pr_url }}" target="_blank" class="text-xs text-text-muted hover:text-terminal transition-colors">
                                    View on GitHub
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($merge->user->avatar_url)
                                        <img src="{{ $merge->user->avatar_url }}" alt="" class="w-6 h-6 rounded-full">
                                    @endif
                                    <span class="text-sm text-text-muted">{{ $merge->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-surface-overlay text-xs font-mono text-text-muted">{{ $merge->merge_method }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-text">{{ $merge->scheduled_at->format('M j, Y') }}</div>
                                <div class="text-xs text-text-muted">{{ $merge->scheduled_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($merge->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-surface-overlay text-text-muted text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-text-muted"></span>
                                        Pending
                                    </span>
                                @elseif($merge->status === 'processing')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-processing/10 text-processing text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-processing animate-pulse"></span>
                                        Processing
                                    </span>
                                @elseif($merge->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-terminal/10 text-terminal text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-terminal"></span>
                                        Completed
                                    </span>
                                @elseif($merge->status === 'failed')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-error/10 text-error text-xs font-medium" title="{{ $merge->error_message }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                        Failed
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-subtle">
                                No merges scheduled yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($merges->hasPages())
            <div class="mt-6">
                {{ $merges->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
