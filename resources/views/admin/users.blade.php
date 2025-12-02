<x-layouts.app title="Manage Users">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <header class="mb-10">
            <div class="flex items-center gap-2 text-sm text-text-muted mb-4">
                <a href="/admin" class="hover:text-text transition-colors">Admin</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-text">Users</span>
            </div>
            <h1 class="text-3xl font-bold text-text mb-2">Manage Users</h1>
            <p class="text-text-muted">View and manage all registered user accounts.</p>
        </header>

        <div class="card overflow-hidden">
            <table class="w-full">
                <thead class="bg-surface-raised">
                    <tr class="text-left text-sm text-text-muted">
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Email</th>
                        <th class="px-6 py-4 font-medium">Merges</th>
                        <th class="px-6 py-4 font-medium">Joined</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse($users as $user)
                        <tr class="hover:bg-surface-raised/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-surface-overlay flex items-center justify-center text-text-muted text-sm font-medium">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-text">{{ $user->name }}</div>
                                        @if($user->is_admin)
                                            <span class="text-xs text-terminal">Admin</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted font-mono">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $user->scheduledMerges()->count() }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-6 py-4">
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
                            </td>
                            <td class="px-6 py-4">
                                <a href="/admin/users/{{ $user->id }}" class="text-sm text-text-muted hover:text-text transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-text-subtle">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
