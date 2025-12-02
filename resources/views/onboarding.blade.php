<x-layouts.guest title="Setup">
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-lg">
            {{-- Progress indicator --}}
            <div class="flex items-center justify-center gap-3 mb-12">
                <div class="w-8 h-8 rounded-full bg-terminal flex items-center justify-center text-void text-sm font-semibold">1</div>
                <div class="w-16 h-px bg-terminal"></div>
                <div class="w-8 h-8 rounded-full bg-surface-raised border border-border flex items-center justify-center text-text-muted text-sm font-semibold">2</div>
            </div>

            {{-- Welcome message --}}
            <div class="text-center mb-10">
                <h1 class="text-2xl font-bold text-text mb-3">Welcome, {{ auth()->user()->name }}!</h1>
                <p class="text-text-muted">Let's get your timezone set up so we can schedule merges accurately.</p>
            </div>

            {{-- Timezone card --}}
            <div class="card p-8">
                <form method="POST" action="/onboarding" class="space-y-6">
                    @csrf

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-text">Select your timezone</label>
                        <p class="text-sm text-text-muted">This ensures your scheduled merges happen at the right time.</p>

                        <select name="timezone" id="timezone" class="input-field" required>
                            <option value="">Choose a timezone...</option>
                            @php
                                $timezones = [
                                    'America/New_York' => 'Eastern Time (ET)',
                                    'America/Chicago' => 'Central Time (CT)',
                                    'America/Denver' => 'Mountain Time (MT)',
                                    'America/Los_Angeles' => 'Pacific Time (PT)',
                                    'America/Anchorage' => 'Alaska Time (AKT)',
                                    'Pacific/Honolulu' => 'Hawaii Time (HT)',
                                    'America/Phoenix' => 'Arizona (No DST)',
                                    'America/Toronto' => 'Toronto (ET)',
                                    'America/Vancouver' => 'Vancouver (PT)',
                                    'Europe/London' => 'London (GMT/BST)',
                                    'Europe/Paris' => 'Paris (CET)',
                                    'Europe/Berlin' => 'Berlin (CET)',
                                    'Europe/Amsterdam' => 'Amsterdam (CET)',
                                    'Europe/Madrid' => 'Madrid (CET)',
                                    'Europe/Rome' => 'Rome (CET)',
                                    'Europe/Stockholm' => 'Stockholm (CET)',
                                    'Europe/Zurich' => 'Zurich (CET)',
                                    'Europe/Warsaw' => 'Warsaw (CET)',
                                    'Europe/Moscow' => 'Moscow (MSK)',
                                    'Asia/Dubai' => 'Dubai (GST)',
                                    'Asia/Kolkata' => 'India (IST)',
                                    'Asia/Singapore' => 'Singapore (SGT)',
                                    'Asia/Hong_Kong' => 'Hong Kong (HKT)',
                                    'Asia/Shanghai' => 'Shanghai (CST)',
                                    'Asia/Tokyo' => 'Tokyo (JST)',
                                    'Asia/Seoul' => 'Seoul (KST)',
                                    'Australia/Sydney' => 'Sydney (AEST)',
                                    'Australia/Melbourne' => 'Melbourne (AEST)',
                                    'Australia/Perth' => 'Perth (AWST)',
                                    'Pacific/Auckland' => 'Auckland (NZST)',
                                    'UTC' => 'UTC',
                                ];
                            @endphp
                            @foreach($timezones as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        @error('timezone')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="glow-line"></div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn-primary flex-1">
                            Continue to Dashboard
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Auto-detect hint --}}
            <p class="text-center text-xs text-text-subtle mt-6">
                <span class="terminal-text" id="detected-timezone"></span>
            </p>
        </div>
    </div>

    <script>
        // Auto-detect and suggest timezone
        const detected = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const select = document.getElementById('timezone');
        const hint = document.getElementById('detected-timezone');

        if (detected) {
            // Try to select the detected timezone
            for (let option of select.options) {
                if (option.value === detected) {
                    option.selected = true;
                    break;
                }
            }
            hint.textContent = `Detected: ${detected}`;
        }
    </script>
</x-layouts.guest>
