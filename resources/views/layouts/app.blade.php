<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('app.brand')) — {{ __('app.brand') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh flex bg-canvas text-ink-primary" x-data="{ mobileNavOpen: false }">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-brand focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        {{ __('app.skip_to_content') }}
    </a>

    <aside class="hidden lg:flex lg:flex-col w-[clamp(14.5rem,18vw,16.5rem)] shrink-0 bg-surface border-r border-border-subtle sticky top-0 min-h-dvh max-h-dvh overflow-y-auto no-print">
        @include('layouts.partials.sidebar-nav', ['isMobile' => false])
    </aside>

    <div x-show="mobileNavOpen" x-cloak class="fixed inset-0 z-30 lg:hidden no-print">
        <div @click="mobileNavOpen = false" class="fixed inset-0 bg-ink-primary/40"></div>
        <aside class="fixed inset-y-0 left-0 z-40 w-[min(85vw,18rem)] bg-surface border-r border-border-subtle shadow-lg flex flex-col min-h-dvh pt-[env(safe-area-inset-top)] pb-[env(safe-area-inset-bottom)]">
            @include('layouts.partials.sidebar-nav', ['isMobile' => true])
        </aside>
    </div>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="sticky top-0 z-10 bg-surface/95 backdrop-blur-xs border-b border-border-subtle min-h-16 px-[clamp(1rem,3vw,1.5rem)] flex items-center justify-between gap-3 no-print">
            <div class="flex items-center gap-2.5 min-w-0">
                <button
                    type="button"
                    @click="mobileNavOpen = true"
                    aria-label="{{ __('app.common.open_menu') }}"
                    class="lg:hidden inline-flex items-center justify-center rounded-lg border border-border-strong bg-surface min-h-11 min-w-11 text-ink-primary cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand shrink-0"
                >
                    <x-ui.icon name="menu" class="w-5 h-5" />
                </button>
                <div class="min-w-0">
                    <p class="text-sm sm:text-base font-semibold text-ink-primary truncate">
                        @yield('header_title', __('app.brand'))
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <x-ui.locale-switcher compact />

                <a
                    href="{{ route('notifications.index') }}"
                    aria-label="{{ __('app.nav.notifications') }}"
                    class="relative inline-flex items-center justify-center rounded-lg border border-border-subtle bg-surface hover:bg-surface-subtle min-h-11 min-w-11 text-ink-secondary hover:text-ink-primary transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                >
                    <x-ui.icon name="bell" class="w-4 h-4" />
                    @if (($unreadNotificationCount ?? 0) > 0)
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-signal text-white font-mono text-[10px] font-semibold min-w-5 h-5 px-1 tabular-nums">
                            {{ $unreadNotificationCount }}
                        </span>
                    @endif
                </a>

                <div class="relative" x-data="{ menuOpen: false }">
                    <button
                        type="button"
                        @click="menuOpen = !menuOpen"
                        @keydown.escape.window="menuOpen = false"
                        class="inline-flex items-center gap-2 rounded-lg border border-border-subtle bg-surface hover:bg-surface-subtle px-2.5 min-h-11 text-left cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                    >
                        <span class="w-7 h-7 rounded-md bg-brand-tint text-brand font-semibold text-xs inline-flex items-center justify-center shrink-0">
                            {{ auth()->user()->initials() }}
                        </span>
                        <span class="hidden sm:block min-w-0 max-w-36">
                            <span class="block text-xs font-semibold text-ink-primary truncate">{{ auth()->user()->name }}</span>
                            <span class="block text-[11px] text-ink-muted truncate">{{ auth()->user()->role->label() }}</span>
                        </span>
                        <x-ui.icon name="chevron-down" class="w-3.5 h-3.5 text-ink-muted shrink-0" />
                    </button>

                    <div
                        x-show="menuOpen"
                        x-cloak
                        @click.outside="menuOpen = false"
                        x-transition.opacity.duration.150ms
                        class="absolute right-0 mt-2 w-64 rounded-xl bg-surface border border-border-subtle shadow-lg z-20 p-2 space-y-2"
                    >
                        <div class="px-2.5 py-2 border-b border-border-subtle">
                            <p class="text-sm font-semibold text-ink-primary truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-ink-muted truncate">{{ auth()->user()->campus_unit }}</p>
                            <div class="mt-1.5">
                                <x-ui.badge :tone="auth()->user()->role->badgeTone()">
                                    {{ auth()->user()->role->label() }}
                                </x-ui.badge>
                            </div>
                        </div>

                        @if (config('demo.enabled'))
                            <div class="px-1.5 py-1 border-b border-border-subtle">
                                <p class="px-1.5 pb-1 text-[11px] font-medium text-ink-muted">
                                    {{ __('app.nav.switch_role') }} · {{ __('app.nav.demo_session_caption') }}
                                </p>
                                <div class="space-y-1">
                                    @foreach (config('demo.personas') as $personaKey => $personaData)
                                        @php $isCurrent = auth()->user()->role->value === $personaKey; @endphp
                                        <form method="POST" action="{{ route('demo.quick-login') }}">
                                            @csrf
                                            <input type="hidden" name="persona" value="{{ $personaKey }}">
                                            <button
                                                type="submit"
                                                @if ($isCurrent) disabled @endif
                                                class="w-full flex items-center justify-between gap-2 rounded-lg px-2.5 min-h-9 text-xs text-left transition-colors duration-150 cursor-pointer disabled:cursor-default {{ $isCurrent ? 'bg-brand-tint text-brand font-semibold' : 'text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary' }}"
                                            >
                                                <span class="truncate">{{ $personaData['name'] }}</span>
                                                <span class="text-[11px] text-ink-muted shrink-0">
                                                    {{ $isCurrent ? __('app.nav.active_role') : __('enums.role.'.$personaKey) }}
                                                </span>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full flex items-center gap-2 rounded-lg px-2.5 min-h-10 text-xs font-medium text-danger hover:bg-status-danger-bg cursor-pointer transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                            >
                                <x-ui.icon name="log-out" class="w-4 h-4 shrink-0" />
                                <span>{{ __('app.nav.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main id="main-content" class="flex-1 min-w-0 w-full max-w-[92rem] mx-auto px-[clamp(1rem,3vw,2rem)] py-6 space-y-6">
            @if (session('status'))
                <x-ui.alert tone="success" class="no-print">{{ session('status') }}</x-ui.alert>
            @endif

            @if (session('error'))
                <x-ui.alert tone="error" class="no-print">{{ session('error') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert tone="error" class="no-print">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
