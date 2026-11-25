<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('landing.meta_title'))</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh flex flex-col bg-canvas text-ink-primary" x-data="{ mobileOpen: false }">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-brand focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        {{ __('app.skip_to_content') }}
    </a>

    <header class="sticky top-0 z-10 bg-canvas/95 backdrop-blur-xs border-b border-border-subtle no-print">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] min-h-16 flex items-center justify-between gap-3">
            <a href="{{ route('public.home') }}" class="inline-flex items-center gap-2.5 text-ink-primary font-bold tracking-tight text-base sm:text-lg min-h-11 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand rounded-lg px-1">
                <span class="w-7 h-7 rounded-md bg-brand text-white inline-flex items-center justify-center shrink-0">
                    <x-ui.icon name="shield-check" class="w-4 h-4" />
                </span>
                <span>{{ __('app.brand') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-ink-secondary" aria-label="Primary navigation">
                <a href="{{ route('public.home') }}#fitur" class="hover:text-ink-primary transition-colors duration-150 cursor-pointer py-2">{{ __('app.nav.features') }}</a>
                <a href="{{ route('public.home') }}#alur" class="hover:text-ink-primary transition-colors duration-150 cursor-pointer py-2">{{ __('app.nav.workflow') }}</a>
                <a href="{{ route('public.docs') }}" class="hover:text-ink-primary transition-colors duration-150 cursor-pointer py-2 {{ request()->routeIs('public.docs') ? 'text-brand font-semibold' : '' }}">
                    {{ __('app.nav.docs') }}
                </a>
            </nav>

            <div class="flex items-center gap-2">
                <x-ui.locale-switcher compact />
                <div class="hidden sm:block">
                    @if (config('demo.enabled'))
                        <x-ui.button :href="route('demo.login')" size="sm">
                            {{ __('app.nav.explore_demo') }}
                        </x-ui.button>
                    @else
                        <x-ui.button :href="route('public.docs')" size="sm" variant="secondary">
                            {{ __('app.nav.docs') }}
                        </x-ui.button>
                    @endif
                </div>
                <button
                    type="button"
                    @click="mobileOpen = true"
                    aria-label="{{ __('app.common.open_menu') }}"
                    class="md:hidden inline-flex items-center justify-center rounded-lg border border-border-strong bg-surface min-h-11 min-w-11 text-ink-primary cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                >
                    <x-ui.icon name="menu" class="w-5 h-5" />
                </button>
            </div>
        </div>
    </header>

    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-30 md:hidden">
        <div @click="mobileOpen = false" class="fixed inset-0 bg-ink-primary/40"></div>
        <div class="fixed inset-y-0 right-0 z-40 w-[min(85vw,19rem)] bg-surface border-l border-border-subtle shadow-lg flex flex-col pt-[env(safe-area-inset-top)] pb-[env(safe-area-inset-bottom)]">
            <div class="flex items-center justify-between px-4 min-h-16 border-b border-border-subtle">
                <span class="font-bold text-ink-primary">{{ __('app.brand') }}</span>
                <button
                    type="button"
                    @click="mobileOpen = false"
                    aria-label="{{ __('app.common.close_menu') }}"
                    class="inline-flex items-center justify-center rounded-lg min-h-11 min-w-11 text-ink-secondary hover:bg-surface-subtle cursor-pointer"
                >
                    <x-ui.icon name="x" class="w-5 h-5" />
                </button>
            </div>
            <nav class="flex-1 p-4 space-y-2 text-sm font-medium">
                <a @click="mobileOpen = false" href="{{ route('public.home') }}#fitur" class="flex items-center min-h-11 px-3 rounded-lg text-ink-primary hover:bg-surface-subtle">{{ __('app.nav.features') }}</a>
                <a @click="mobileOpen = false" href="{{ route('public.home') }}#alur" class="flex items-center min-h-11 px-3 rounded-lg text-ink-primary hover:bg-surface-subtle">{{ __('app.nav.workflow') }}</a>
                <a @click="mobileOpen = false" href="{{ route('public.docs') }}" class="flex items-center min-h-11 px-3 rounded-lg text-ink-primary hover:bg-surface-subtle">{{ __('app.nav.docs') }}</a>
                @if (config('demo.enabled'))
                    <div class="pt-3">
                        <x-ui.button :href="route('demo.login')" class="w-full">
                            {{ __('app.nav.explore_demo') }}
                        </x-ui.button>
                    </div>
                @endif
            </nav>
        </div>
    </div>

    <main id="main-content" class="flex-1 min-w-0">
        @yield('content')
    </main>

    <footer class="bg-surface border-t border-border-subtle py-10 no-print">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
            <div class="min-w-0 space-y-2">
                <p class="font-bold text-ink-primary">{{ __('app.brand') }}</p>
                <p class="text-ink-secondary text-xs leading-relaxed">{{ __('app.tagline') }}</p>
                <p class="text-ink-muted text-xs leading-relaxed">{{ __('app.demo.transparency_line') }}</p>
            </div>

            <div class="min-w-0 space-y-2">
                <p class="font-semibold text-ink-primary">{{ __('landing.footer.navigation_title') }}</p>
                <ul class="space-y-1.5 text-ink-secondary">
                    <li><a href="{{ route('public.home') }}" class="hover:text-brand underline-offset-4 hover:underline">{{ __('app.common.back_to_home') }}</a></li>
                    <li><a href="{{ route('public.docs') }}" class="hover:text-brand underline-offset-4 hover:underline">{{ __('app.nav.docs') }}</a></li>
                    @if (config('demo.enabled'))
                        <li><a href="{{ route('demo.login') }}" class="hover:text-brand underline-offset-4 hover:underline">{{ __('app.nav.explore_demo') }}</a></li>
                    @endif
                </ul>
            </div>

            <div class="min-w-0 space-y-2">
                <p class="font-semibold text-ink-primary">{{ __('landing.footer.contact_title') }}</p>
                <p class="text-xs text-ink-secondary leading-relaxed">{{ __('landing.footer.contact_note') }}</p>
                <a href="mailto:{{ config('demo.contact_email') }}" class="inline-flex items-center gap-1.5 font-mono text-xs text-brand hover:underline break-all">
                    <x-ui.icon name="mail" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ config('demo.contact_email') }}</span>
                </a>
                <div class="pt-2">
                    <x-ui.locale-switcher compact />
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
