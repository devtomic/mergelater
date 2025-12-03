<x-layouts.guest title="Schedule GitHub PR Merges">
    {{-- Fixed Navigation --}}
    <nav x-data="{ scrolled: false, mobileOpen: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
         :class="{ 'bg-void/90 backdrop-blur-xl border-b border-border-subtle': scrolled, 'bg-transparent': !scrolled }"
         class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-6xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-lg bg-surface border border-border flex items-center justify-center group-hover:border-terminal/50 transition-colors">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-text">MergeLater</span>
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#pain-points" class="text-sm text-text-muted hover:text-text transition-colors">Why</a>
                    <a href="#how-it-works" class="text-sm text-text-muted hover:text-text transition-colors">How It Works</a>
                    <a href="#features" class="text-sm text-text-muted hover:text-text transition-colors">Features</a>
                    <a href="#use-cases" class="text-sm text-text-muted hover:text-text transition-colors">Use Cases</a>
                </div>

                {{-- Login Button --}}
                <div class="flex items-center gap-4">
                    <a href="/login" class="hidden md:inline-flex btn-secondary text-sm">
                        Login
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    {{-- Mobile Menu Button --}}
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-text-muted hover:text-text">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden mt-4 pb-4 border-t border-border-subtle pt-4">
                <div class="flex flex-col gap-4">
                    <a href="#pain-points" @click="mobileOpen = false" class="text-text-muted hover:text-text transition-colors">Why</a>
                    <a href="#how-it-works" @click="mobileOpen = false" class="text-text-muted hover:text-text transition-colors">How It Works</a>
                    <a href="#features" @click="mobileOpen = false" class="text-text-muted hover:text-text transition-colors">Features</a>
                    <a href="#use-cases" @click="mobileOpen = false" class="text-text-muted hover:text-text transition-colors">Use Cases</a>
                    <a href="/login" class="btn-secondary text-sm w-fit">Login</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="min-h-screen flex items-center pt-20 pb-16 px-6">
        <div class="max-w-6xl mx-auto w-full">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Left: Text Content --}}
                <div class="section-reveal">
                    {{-- Early Access Badge --}}
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-terminal/30 bg-terminal/5 mb-8 animate-pulse-slow">
                        <span class="w-2 h-2 rounded-full bg-terminal animate-pulse"></span>
                        <span class="text-sm text-terminal font-medium">Early Access</span>
                    </div>

                    {{-- Main Headline --}}
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-text leading-tight mb-6">
                        Merge PRs on<br>
                        <span class="text-gradient">your</span> schedule
                    </h1>

                    {{-- Subheadline --}}
                    <p class="text-lg text-text-muted leading-relaxed mb-8 max-w-lg">
                        Stop timing deploys around your calendar. Schedule GitHub merges for the perfect moment—then go live your life.
                    </p>

                    {{-- Kit Embed --}}
                    <div class="mb-6">
                        <script async data-uid="a6de31828a" src="https://macademy.kit.com/a6de31828a/index.js"></script>
                    </div>

                    {{-- FOMO Indicator --}}
                    <div class="inline-flex items-center gap-2 text-sm text-text-muted">
                        <svg class="w-4 h-4 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Limited spots available</span>
                        <span class="text-text-subtle">•</span>
                        <span>Be first when we launch</span>
                    </div>
                </div>

                {{-- Right: Floating Terminal Mockup --}}
                <div class="relative section-reveal" style="animation-delay: 200ms;">
                    {{-- Glow Effect Behind --}}
                    <div class="absolute -inset-4 bg-terminal/10 rounded-3xl blur-3xl animate-glow"></div>

                    {{-- Terminal Card --}}
                    <div class="relative card p-6 transform rotate-1 hover:rotate-0 transition-transform duration-500 animate-float">
                        {{-- Terminal Header --}}
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                            <span class="ml-2 text-xs text-text-subtle font-mono">Scheduled Merges</span>
                        </div>

                        {{-- Mock Merge Queue --}}
                        <div class="space-y-3">
                            {{-- Merge Item 1 - Completed --}}
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-raised border border-border-subtle">
                                <div class="w-2 h-2 rounded-full bg-terminal"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-text font-mono truncate">feat/black-friday-promo</p>
                                    <p class="text-xs text-text-subtle">Merged at 12:00 AM</p>
                                </div>
                                <span class="text-xs text-terminal">Completed</span>
                            </div>

                            {{-- Merge Item 2 - Processing --}}
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-raised border border-terminal/30">
                                <div class="w-2 h-2 rounded-full bg-processing animate-pulse"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-text font-mono truncate">fix/checkout-validation</p>
                                    <p class="text-xs text-text-subtle">Merging now...</p>
                                </div>
                                <span class="text-xs text-processing">Processing</span>
                            </div>

                            {{-- Merge Item 3 - Pending --}}
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-raised border border-border-subtle">
                                <div class="w-2 h-2 rounded-full bg-text-subtle"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-text font-mono truncate">chore/dependency-update</p>
                                    <p class="text-xs text-text-subtle">Scheduled for 9:00 AM</p>
                                </div>
                                <span class="text-xs text-text-muted">Pending</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pain Points Section --}}
    <section id="pain-points" class="py-24 px-6 scroll-mt-20">
        <div class="max-w-6xl mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16 section-reveal">
                <h2 class="text-3xl sm:text-4xl font-bold text-text mb-4">We've all been there.</h2>
                <p class="text-text-muted max-w-2xl mx-auto">The mental gymnastics of timing a merge just right. The anxiety of "should I wait?" Let's fix that.</p>
            </div>

            {{-- Pain Point Cards --}}
            <div class="grid sm:grid-cols-2 gap-6">
                {{-- Card 1: Friday Merge --}}
                <div class="card p-6 hover:-translate-y-1 transition-all duration-300 section-reveal" style="animation-delay: 100ms;">
                    <div class="w-12 h-12 rounded-xl bg-surface-raised border border-border flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text mb-2">The Friday 4:59 PM Merge</h3>
                    <p class="text-text-muted text-sm leading-relaxed">Teammates approve at EOD. Do you merge and risk the weekend, or wait until Monday and lose momentum?</p>
                </div>

                {{-- Card 2: Timezone --}}
                <div class="card p-6 hover:-translate-y-1 transition-all duration-300 section-reveal" style="animation-delay: 200ms;">
                    <div class="w-12 h-12 rounded-xl bg-surface-raised border border-border flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text mb-2">The Timezone Tango</h3>
                    <p class="text-text-muted text-sm leading-relaxed">Your team spans 12 hours. Someone's always asleep when code needs to ship. Coordination shouldn't be this hard.</p>
                </div>

                {{-- Card 3: 3AM Launch --}}
                <div class="card p-6 hover:-translate-y-1 transition-all duration-300 section-reveal" style="animation-delay: 300ms;">
                    <div class="w-12 h-12 rounded-xl bg-surface-raised border border-border flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text mb-2">The 3 AM Launch</h3>
                    <p class="text-text-muted text-sm leading-relaxed">Black Friday starts at midnight. Cyber Monday at 12:01 AM. Your deploy shouldn't require an alarm clock.</p>
                </div>

                {{-- Card 4: Procrastination --}}
                <div class="card p-6 hover:-translate-y-1 transition-all duration-300 section-reveal" style="animation-delay: 400ms;">
                    <div class="w-12 h-12 rounded-xl bg-surface-raised border border-border flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-text mb-2">The "I'll Do It Later"</h3>
                    <p class="text-text-muted text-sm leading-relaxed">You said you'd merge after lunch. It's now 6 PM and that PR is still open. Sound familiar?</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="how-it-works" class="py-24 px-6 scroll-mt-20">
        <div class="max-w-6xl mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16 section-reveal">
                <h2 class="text-3xl sm:text-4xl font-bold text-text mb-4">Three steps to freedom</h2>
                <p class="text-text-muted">Schedule a merge in under 30 seconds. Then forget about it.</p>
            </div>

            {{-- Steps --}}
            <div class="grid md:grid-cols-3 gap-8 relative">
                {{-- Connecting Line --}}
                <div class="hidden md:block absolute top-16 left-1/6 right-1/6 h-px glow-line"></div>

                {{-- Step 1 --}}
                <div class="text-center section-reveal" style="animation-delay: 100ms;">
                    <div class="w-16 h-16 rounded-2xl bg-surface border border-border flex items-center justify-center mx-auto mb-6 relative">
                        <span class="text-2xl font-bold text-gradient">1</span>
                        <div class="absolute -inset-1 bg-terminal/10 rounded-2xl blur-xl -z-10 animate-glow"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-text mb-2">Connect GitHub</h3>
                    <p class="text-text-muted text-sm">One-click OAuth. We only need repo access to merge your PRs.</p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center section-reveal" style="animation-delay: 200ms;">
                    <div class="w-16 h-16 rounded-2xl bg-surface border border-border flex items-center justify-center mx-auto mb-6 relative">
                        <span class="text-2xl font-bold text-gradient">2</span>
                        <div class="absolute -inset-1 bg-terminal/10 rounded-2xl blur-xl -z-10 animate-glow"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-text mb-2">Pick Your Moment</h3>
                    <p class="text-text-muted text-sm">Paste a PR URL, choose your merge method, set the time.</p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center section-reveal" style="animation-delay: 300ms;">
                    <div class="w-16 h-16 rounded-2xl bg-surface border border-border flex items-center justify-center mx-auto mb-6 relative">
                        <span class="text-2xl font-bold text-gradient">3</span>
                        <div class="absolute -inset-1 bg-terminal/10 rounded-2xl blur-xl -z-10 animate-glow"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-text mb-2">Go Touch Grass</h3>
                    <p class="text-text-muted text-sm">We merge it. You get notified. Life goes on.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-24 px-6 scroll-mt-20">
        <div class="max-w-6xl mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16 section-reveal">
                <h2 class="text-3xl sm:text-4xl font-bold text-text mb-4">Built for developers who value their time</h2>
                <p class="text-text-muted">Every feature designed to give you back control.</p>
            </div>

            {{-- Features Grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Feature 1 --}}
                <div class="card p-6 section-reveal" style="animation-delay: 100ms;">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-text mb-2">Smart Scheduling</h3>
                    <p class="text-text-muted text-sm">Timezone-aware scheduling that works for teams across the globe.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="card p-6 section-reveal" style="animation-delay: 150ms;">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-text mb-2">Merge Methods</h3>
                    <p class="text-text-muted text-sm">Squash, merge commit, or rebase—your choice, always.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="card p-6 section-reveal" style="animation-delay: 200ms;">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-text mb-2">Instant Notifications</h3>
                    <p class="text-text-muted text-sm">Email & Slack alerts when it's done (or fails).</p>
                </div>

                {{-- Feature 4 --}}
                <div class="card p-6 section-reveal" style="animation-delay: 250ms;">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-text mb-2">Full Visibility</h3>
                    <p class="text-text-muted text-sm">Dashboard shows all pending merges at a glance.</p>
                </div>

                {{-- Feature 5 --}}
                <div class="card p-6 section-reveal" style="animation-delay: 300ms;">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-terminal" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-text mb-2">GitHub Native</h3>
                    <p class="text-text-muted text-sm">Uses official API, respects branch protections.</p>
                </div>

                {{-- Feature 6 --}}
                <div class="card p-6 section-reveal" style="animation-delay: 350ms;">
                    <div class="w-10 h-10 rounded-lg bg-terminal/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-text mb-2">No Lock-in</h3>
                    <p class="text-text-muted text-sm">Cancel anytime. Your PRs stay on GitHub.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Use Cases Section --}}
    <section id="use-cases" class="py-24 px-6 scroll-mt-20">
        <div class="max-w-6xl mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16 section-reveal">
                <h2 class="text-3xl sm:text-4xl font-bold text-text mb-4">Perfect for...</h2>
                <p class="text-text-muted">No matter your workflow, MergeLater fits right in.</p>
            </div>

            {{-- Use Case Cards --}}
            <div class="grid sm:grid-cols-2 gap-8">
                {{-- Use Case 1 --}}
                <div class="flex gap-6 section-reveal" style="animation-delay: 100ms;">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-terminal/20 to-terminal/5 border border-terminal/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-text mb-2">Release Managers</h3>
                        <p class="text-text-muted text-sm leading-relaxed">Queue up merges for your next deployment window. Batch releases without the manual coordination headache.</p>
                    </div>
                </div>

                {{-- Use Case 2 --}}
                <div class="flex gap-6 section-reveal" style="animation-delay: 200ms;">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-terminal/20 to-terminal/5 border border-terminal/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-text mb-2">Remote Teams</h3>
                        <p class="text-text-muted text-sm leading-relaxed">Schedule merges for when reviewers are awake. No more "can someone merge this?" messages at 2 AM.</p>
                    </div>
                </div>

                {{-- Use Case 3 --}}
                <div class="flex gap-6 section-reveal" style="animation-delay: 300ms;">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-terminal/20 to-terminal/5 border border-terminal/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-text mb-2">E-commerce Teams</h3>
                        <p class="text-text-muted text-sm leading-relaxed">Deploy promos exactly when sales start. Black Friday at midnight? Your promo code goes live while you sleep.</p>
                    </div>
                </div>

                {{-- Use Case 4 --}}
                <div class="flex gap-6 section-reveal" style="animation-delay: 400ms;">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-terminal/20 to-terminal/5 border border-terminal/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-text mb-2">On-Call Engineers</h3>
                        <p class="text-text-muted text-sm leading-relaxed">Set it and forget it—no more laptop-on-vacation. Be on the beach, not refreshing your PR page.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA Section --}}
    <section class="py-24 px-6">
        <div class="max-w-2xl mx-auto text-center relative section-reveal">
            {{-- Background Glow --}}
            <div class="absolute -inset-x-20 -inset-y-10 bg-terminal/5 rounded-3xl blur-3xl -z-10"></div>

            <h2 class="text-3xl sm:text-4xl font-bold text-text mb-4">Ready to take back your time?</h2>
            <p class="text-text-muted mb-8">Join the early access waitlist and be first to know when we launch.</p>

            {{-- Kit Embed --}}
            <div class="max-w-md mx-auto">
                <script async data-uid="a6de31828a" src="https://macademy.kit.com/a6de31828a/index.js"></script>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-12 px-6 border-t border-border-subtle">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-surface border border-border flex items-center justify-center">
                        <svg class="w-4 h-4 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-text-muted">&copy; {{ date('Y') }} MergeLater. Made for developers who'd rather be anywhere else when code ships.</span>
                </div>
                <a href="/login" class="text-sm text-text-muted hover:text-terminal transition-colors">
                    Early Access Login →
                </a>
            </div>
        </div>
    </footer>
</x-layouts.guest>
