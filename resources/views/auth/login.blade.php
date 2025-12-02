<x-layouts.guest title="Login">
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            {{-- Logo and branding --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-surface border border-border mb-6 animate-float">
                    <svg class="w-8 h-8 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-text mb-2">MergeLater</h1>
                <p class="text-text-muted">Schedule your GitHub PR merges</p>
            </div>

            {{-- Login card --}}
            <div class="card p-8">
                <div class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-xl font-semibold text-text mb-2">Welcome back</h2>
                        <p class="text-sm text-text-muted">Sign in with your GitHub account to continue</p>
                    </div>

                    <div class="glow-line"></div>

                    <a href="/auth/github" class="btn-primary w-full text-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                        </svg>
                        Continue with GitHub
                    </a>

                    <p class="text-xs text-text-subtle text-center leading-relaxed">
                        By signing in, you grant MergeLater access to merge PRs on repositories you have write access to.
                        <span class="terminal-text">repo</span> scope is required.
                    </p>
                </div>
            </div>

            {{-- Features preview --}}
            <div class="mt-12 grid grid-cols-3 gap-4 text-center">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-lg bg-surface-raised border border-border mx-auto flex items-center justify-center">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-text-muted">Schedule<br>merges</p>
                </div>
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-lg bg-surface-raised border border-border mx-auto flex items-center justify-center">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-xs text-text-muted">Get<br>notified</p>
                </div>
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-lg bg-surface-raised border border-border mx-auto flex items-center justify-center">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-text-muted">Ship<br>safely</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
