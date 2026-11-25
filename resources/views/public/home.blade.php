@extends('layouts.public')

@section('title', __('landing.meta_title'))

@section('content')
    <section class="bg-canvas py-[clamp(2.5rem,6vw,5rem)]">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] grid grid-cols-1 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] gap-8 lg:gap-12 items-center">
            <div class="min-w-0 space-y-5">
                <h1 class="text-[clamp(1.75rem,3.6vw+0.8rem,3.125rem)] leading-[1.12] tracking-[-0.025em] font-bold text-ink-primary text-balance">
                    {{ __('landing.hero.title') }}
                </h1>
                <p class="text-[clamp(0.9375rem,0.2vw+0.88rem,1.0625rem)] leading-[1.6] text-ink-secondary max-w-[58ch]">
                    {{ __('landing.hero.subtitle') }}
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    @if ($demoEnabled)
                        <x-ui.button :href="route('demo.login')" size="lg">
                            <x-ui.icon name="shield-check" class="w-4 h-4" />
                            <span>{{ __('landing.hero.cta_primary') }}</span>
                        </x-ui.button>
                    @endif
                    <x-ui.button :href="route('public.docs')" variant="secondary" size="lg">
                        <x-ui.icon name="file-text" class="w-4 h-4" />
                        <span>{{ __('landing.hero.cta_secondary') }}</span>
                    </x-ui.button>
                </div>

                <div class="border-t border-border-subtle pt-5 mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-medium text-ink-secondary">
                    @foreach (__('landing.hero.trust_items') as $trustItem)
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full bg-brand shrink-0"></span>
                            <span class="truncate">{{ $trustItem }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="min-w-0">
                <div class="bg-surface border border-border-subtle rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-border-subtle pb-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="font-mono text-xs font-semibold text-ink-primary">#TK-2026-0005</span>
                            <x-ui.badge tone="found">{{ __('enums.item_type.found') }}</x-ui.badge>
                        </div>
                        <x-ui.badge tone="brand" mono>{{ __('landing.preview.match_badge') }}</x-ui.badge>
                    </div>

                    <div class="space-y-2 min-w-0">
                        <p class="text-xs text-ink-muted">{{ __('landing.preview.ticket_label') }}</p>
                        <h2 class="text-base sm:text-lg font-semibold text-ink-primary break-words">
                            {{ __('landing.preview.sample_title') }}
                        </h2>
                        <p class="inline-flex items-center gap-1.5 text-xs text-ink-secondary font-mono break-words">
                            <x-ui.icon name="map-pin" class="w-3.5 h-3.5 text-ink-muted shrink-0" />
                            <span>{{ __('landing.preview.zone_label') }}</span>
                        </p>
                    </div>

                    <div class="rounded-xl border border-border-subtle bg-surface-subtle p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-surface border border-border-subtle flex items-center justify-center text-ink-muted shrink-0">
                                <x-ui.icon name="lock" class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-ink-primary break-words">{{ __('landing.preview.privacy_shield') }}</p>
                                <p class="text-[11px] text-ink-muted font-mono">NIM ••••-••••-0142</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="rounded-lg border border-border-subtle p-3 min-w-0">
                            <p class="text-[11px] text-ink-muted">{{ __('enums.custody_type.at_custody_post') }}</p>
                            <p class="text-xs font-medium text-ink-primary mt-0.5 break-words">{{ __('landing.preview.custody_label') }}</p>
                        </div>
                        <div class="rounded-lg border border-border-subtle p-3 min-w-0 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[11px] text-ink-muted">Blind Challenge</p>
                                <p class="text-xs font-medium text-brand mt-0.5">{{ __('landing.preview.challenge_badge') }}</p>
                            </div>
                            <x-ui.icon name="shield-check" class="w-4 h-4 text-brand shrink-0" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="bg-surface border-y border-border-subtle py-[clamp(2.5rem,5vw,4.5rem)] scroll-mt-20">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] space-y-8">
            <div class="max-w-2xl space-y-2">
                <h2 class="text-[clamp(1.25rem,1.8vw+0.85rem,1.75rem)] leading-[1.25] font-semibold text-ink-primary text-balance">
                    {{ __('landing.capabilities.heading') }}
                </h2>
                <p class="text-sm sm:text-base text-ink-secondary leading-relaxed">
                    {{ __('landing.capabilities.subheading') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach (__('landing.capabilities.items') as $cap)
                    <article class="rounded-xl border border-border-subtle bg-canvas/50 p-5 sm:p-6 space-y-2.5 min-w-0">
                        <span class="inline-block font-mono text-xs font-semibold text-brand bg-brand-tint px-2 py-0.5 rounded-md">
                            {{ $cap['number'] }}
                        </span>
                        <h3 class="text-base font-semibold text-ink-primary break-words">{{ $cap['title'] }}</h3>
                        <p class="text-sm text-ink-secondary leading-relaxed break-words">{{ $cap['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="alur" class="bg-canvas py-[clamp(2.5rem,5vw,4.5rem)] scroll-mt-20">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] space-y-8">
            <h2 class="text-[clamp(1.25rem,1.8vw+0.85rem,1.75rem)] leading-[1.25] font-semibold text-ink-primary text-balance">
                {{ __('landing.workflow.heading') }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach (__('landing.workflow.steps') as $step)
                    <div class="rounded-xl border border-border-subtle bg-surface p-5 space-y-2 min-w-0">
                        <span class="font-mono text-xs font-semibold text-ink-muted">{{ $step['step'] }}</span>
                        <h3 class="text-sm font-semibold text-ink-primary break-words">{{ $step['title'] }}</h3>
                        <p class="text-xs sm:text-sm text-ink-secondary leading-relaxed break-words">{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-surface border-y border-border-subtle py-[clamp(2.5rem,5vw,4.5rem)]">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="max-w-2xl space-y-1.5">
                    <h2 class="text-[clamp(1.25rem,1.8vw+0.85rem,1.75rem)] leading-[1.25] font-semibold text-ink-primary">
                        {{ __('landing.roles.heading') }}
                    </h2>
                    <p class="text-sm text-ink-secondary">{{ __('landing.roles.subheading') }}</p>
                </div>
                @if ($demoEnabled)
                    <x-ui.button :href="route('demo.login')" variant="secondary" size="sm">
                        {{ __('landing.roles.cta') }}
                    </x-ui.button>
                @endif
            </div>

            <div class="divide-y divide-border-subtle rounded-xl border border-border-subtle bg-canvas/40">
                @foreach (__('landing.roles.items') as $roleRow)
                    <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-[15rem_minmax(0,1fr)] gap-2 md:gap-6 items-start">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-ink-primary">{{ $roleRow['role'] }}</p>
                            <p class="text-xs text-ink-muted">{{ $roleRow['persona'] }}</p>
                        </div>
                        <p class="text-sm text-ink-secondary leading-relaxed min-w-0 break-words">{{ $roleRow['scope'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-canvas py-[clamp(2.5rem,5vw,4rem)]">
        <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)]">
            <div class="rounded-2xl border border-border-subtle bg-surface p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl min-w-0">
                    <h2 class="text-[clamp(1.125rem,1.5vw+0.8rem,1.5rem)] font-semibold text-ink-primary text-balance">
                        {{ __('landing.final_cta.heading') }}
                    </h2>
                    <p class="text-sm text-ink-secondary leading-relaxed">{{ __('landing.final_cta.description') }}</p>
                    <p class="text-xs text-ink-muted pt-1">{{ __('app.demo.transparency_line') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    @if ($demoEnabled)
                        <x-ui.button :href="route('demo.login')" size="md">
                            {{ __('landing.final_cta.primary') }}
                        </x-ui.button>
                    @endif
                    <x-ui.button :href="route('public.docs')" variant="secondary" size="md">
                        {{ __('landing.final_cta.secondary') }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>
@endsection
