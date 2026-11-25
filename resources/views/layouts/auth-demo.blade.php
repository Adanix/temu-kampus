<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('app.demo.login_title').' — '.__('app.brand'))</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh flex flex-col bg-canvas text-ink-primary">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-brand focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        {{ __('app.skip_to_content') }}
    </a>

    <header class="border-b border-border-subtle bg-surface/90">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] min-h-16 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('public.home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink-secondary hover:text-ink-primary min-h-11 px-2 rounded-lg cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand">
                    <x-ui.icon name="arrow-left" class="w-4 h-4 shrink-0" />
                    <span class="truncate">{{ __('app.common.back_to_home') }}</span>
                </a>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <x-ui.locale-switcher compact />
            </div>
        </div>
    </header>

    <main id="main-content" class="flex-1 flex flex-col justify-center w-full max-w-4xl mx-auto px-[clamp(1rem,3vw,2rem)] py-8 min-w-0">
        @yield('content')
    </main>

    <footer class="border-t border-border-subtle bg-surface py-4 text-center text-xs text-ink-muted">
        <div class="w-full max-w-4xl mx-auto px-4 flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
            <span>{{ __('app.demo.transparency_line') }}</span>
            <a href="{{ route('public.docs') }}" class="text-brand hover:underline">{{ __('app.nav.docs') }}</a>
            <a href="mailto:{{ config('demo.contact_email') }}" class="font-mono text-brand hover:underline">{{ config('demo.contact_email') }}</a>
        </div>
    </footer>
</body>
</html>
