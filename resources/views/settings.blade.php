<x-layouts.app title="Settings">
    <div class="max-w-2xl mx-auto px-6 py-12">
        <header class="mb-10">
            <h1 class="text-3xl font-bold text-text mb-2">Settings</h1>
            <p class="text-text-muted">Configure your notification preferences and account settings.</p>
        </header>

        <form method="POST" action="/settings" class="space-y-8">
            @csrf

            {{-- Timezone Section --}}
            <section class="card p-6">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 border border-terminal/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-text">Timezone</h2>
                        <p class="text-sm text-text-muted mt-1">All scheduled merge times will be displayed in this timezone.</p>
                    </div>
                </div>

                <div class="relative">
                    <select name="timezone" class="input-field appearance-none cursor-pointer pr-10">
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ auth()->user()->timezone === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                @error('timezone')
                    <p class="text-error text-sm mt-2">{{ $message }}</p>
                @enderror
            </section>

            {{-- Email Notifications Section --}}
            <section class="card p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 border border-terminal/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-text">Email Notifications</h2>
                                <p class="text-sm text-text-muted mt-1">Receive emails when your scheduled merges complete or fail.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="email_notifications" value="0">
                                <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" {{ auth()->user()->email_notifications ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-surface-overlay rounded-full peer-checked:bg-terminal/30 peer-checked:border-terminal transition-all border border-border"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-text-muted rounded-full peer-checked:translate-x-5 peer-checked:bg-terminal transition-all"></div>
                            </label>
                        </div>
                        <div class="mt-4 pt-4 border-t border-border/50">
                            <p class="text-sm text-text-muted">
                                Notifications sent to: <span class="text-text font-medium">{{ auth()->user()->email }}</span>
                            </p>
                            <p class="text-xs text-text-subtle mt-1">
                                Synced from GitHub · <a href="https://github.com/settings/emails" target="_blank" rel="noopener noreferrer" class="text-terminal hover:underline">Change on GitHub <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a> · Re-authenticate below to sync changes
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Slack Webhook Section --}}
            <section class="card p-6">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 border border-terminal/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-terminal" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zm1.271 0a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zm2.521-10.123a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zm0 1.271a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zm10.122 2.521a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zm-1.271 0a2.528 2.528 0 0 1-2.52 2.521 2.528 2.528 0 0 1-2.521-2.521V2.522A2.528 2.528 0 0 1 15.165 0a2.528 2.528 0 0 1 2.52 2.522v6.312zm-2.52 10.122a2.528 2.528 0 0 1 2.52 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.521-2.522v-2.522h2.521zm0-1.271a2.527 2.527 0 0 1-2.521-2.52 2.527 2.527 0 0 1 2.521-2.521h6.313A2.528 2.528 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.52h-6.313z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-text">Slack Notifications</h2>
                        <p class="text-sm text-text-muted mt-1">Send merge notifications to a Slack channel via webhook.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="slack_webhook_url" class="block text-sm font-medium text-text mb-2">Webhook URL</label>
                        <input
                            type="url"
                            name="slack_webhook_url"
                            id="slack_webhook_url"
                            class="input-field"
                            placeholder="https://hooks.slack.com/services/..."
                            value="{{ auth()->user()->slack_webhook_url }}"
                        >
                        @error('slack_webhook_url')
                            <p class="text-error text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <p class="text-xs text-text-subtle">
                        <a href="https://api.slack.com/apps?new_app=1" target="_blank" rel="noopener noreferrer" class="text-terminal hover:underline">Create a Slack app <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>, then get your webhook URL from Features → Incoming Webhooks.
                    </p>
                </div>
            </section>

            {{-- Danger Zone --}}
            <section class="card p-6 border-error/20">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-error/10 border border-error/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-text">Re-authenticate with GitHub</h2>
                        <p class="text-sm text-text-muted mt-1">If your token has expired or you need to update permissions, re-authenticate with GitHub.</p>
                        <a href="/auth/github" class="btn-secondary mt-4 inline-flex text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                            Re-authenticate
                        </a>
                    </div>
                </div>
            </section>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
