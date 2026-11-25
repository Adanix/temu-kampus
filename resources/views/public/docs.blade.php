@extends('layouts.public')

@section('title', __('docs.meta_title'))

@section('content')
    <div class="w-full max-w-[min(100%,80rem)] mx-auto px-[clamp(1rem,3vw,2rem)] py-[clamp(2rem,4vw,3.5rem)]" x-data="{ tocOpen: false }">
        <div class="border-b border-border-subtle pb-6 mb-8 space-y-2">
            <h1 class="text-[clamp(1.5rem,2vw+0.85rem,2.125rem)] leading-[1.2] tracking-[-0.02em] font-bold text-ink-primary text-balance">
                {{ __('docs.title') }}
            </h1>
            <p class="text-sm sm:text-base text-ink-secondary max-w-[65ch] leading-relaxed">
                {{ __('docs.subtitle') }}
            </p>
        </div>

        <div class="lg:hidden mb-6">
            <button
                type="button"
                @click="tocOpen = !tocOpen"
                :aria-expanded="tocOpen.toString()"
                class="w-full flex items-center justify-between gap-2 rounded-xl border border-border-subtle bg-surface px-4 min-h-11 text-sm font-semibold text-ink-primary cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
            >
                <span>{{ __('docs.toc_title') }}</span>
                <x-ui.icon name="chevron-down" class="w-4 h-4 transition-transform duration-150" x-bind:class="tocOpen ? 'rotate-180' : ''" />
            </button>
            <nav x-show="tocOpen" x-cloak class="mt-2 rounded-xl border border-border-subtle bg-surface p-3 space-y-1 text-sm" aria-label="{{ __('docs.toc_title') }}">
                <a @click="tocOpen = false" href="#overview" class="block px-3 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.overview.title') }}</a>
                <a @click="tocOpen = false" href="#personas" class="block px-3 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.personas.title') }}</a>
                <a @click="tocOpen = false" href="#modules" class="block px-3 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.modules.title') }}</a>
                <a @click="tocOpen = false" href="#workflows" class="block px-3 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.workflows.title') }}</a>
                <a @click="tocOpen = false" href="#matching-lifecycle" class="block px-3 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.matching.title') }}</a>
                <a @click="tocOpen = false" href="#simulations" class="block px-3 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.simulations.title') }}</a>
                <a @click="tocOpen = false" href="#customization" class="block px-3 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.customization.title') }}</a>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[16rem_minmax(0,1fr)] gap-8 items-start">
            <aside class="hidden lg:block sticky top-24 self-start bg-surface border border-border-subtle rounded-xl p-4 space-y-2">
                <p class="text-xs font-semibold text-ink-muted px-2">{{ __('docs.toc_title') }}</p>
                <nav class="space-y-1 text-sm" aria-label="{{ __('docs.toc_title') }}">
                    <a href="#overview" class="block px-2.5 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.overview.title') }}</a>
                    <a href="#personas" class="block px-2.5 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.personas.title') }}</a>
                    <a href="#modules" class="block px-2.5 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.modules.title') }}</a>
                    <a href="#workflows" class="block px-2.5 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.workflows.title') }}</a>
                    <a href="#matching-lifecycle" class="block px-2.5 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.matching.title') }}</a>
                    <a href="#simulations" class="block px-2.5 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.simulations.title') }}</a>
                    <a href="#customization" class="block px-2.5 py-2 rounded-lg text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary">{{ __('docs.sections.customization.title') }}</a>
                </nav>
            </aside>

            <article class="max-w-[70ch] min-w-0 space-y-10">
                <section id="overview" class="space-y-3 scroll-mt-24">
                    <h2 class="text-[clamp(1.125rem,1.2vw+0.8rem,1.375rem)] font-semibold text-ink-primary">{{ __('docs.sections.overview.title') }}</h2>
                    <p class="text-sm sm:text-base text-ink-secondary leading-[1.65]">{{ __('docs.sections.overview.body') }}</p>
                </section>

                <section id="personas" class="space-y-4 scroll-mt-24">
                    <h2 class="text-[clamp(1.125rem,1.2vw+0.8rem,1.375rem)] font-semibold text-ink-primary">{{ __('docs.sections.personas.title') }}</h2>
                    <p class="text-sm sm:text-base text-ink-secondary leading-[1.65]">{{ __('docs.sections.personas.intro') }}</p>

                    <div class="space-y-3">
                        @foreach ($personas as $personaKey => $persona)
                            <div class="rounded-xl border border-border-subtle bg-surface p-4 space-y-1.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-ink-primary">{{ $persona['name'] }}</span>
                                    <x-ui.badge tone="neutral">{{ __('enums.role.'.$personaKey) }}</x-ui.badge>
                                </div>
                                <p class="text-xs text-ink-muted font-mono">{{ $persona['campus_unit'] }}</p>
                                <p class="text-sm text-ink-secondary leading-relaxed">{{ __('app.demo.persona_summaries.'.$personaKey) }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if ($demoEnabled)
                        <div>
                            <x-ui.button :href="route('demo.login')" size="sm">
                                {{ __('app.nav.explore_demo') }}
                            </x-ui.button>
                        </div>
                    @endif
                </section>

                <section id="modules" class="space-y-4 scroll-mt-24">
                    <h2 class="text-[clamp(1.125rem,1.2vw+0.8rem,1.375rem)] font-semibold text-ink-primary">{{ __('docs.sections.modules.title') }}</h2>
                    <div class="space-y-3">
                        @foreach (__('docs.sections.modules.items') as $mod)
                            <div class="rounded-xl border border-border-subtle bg-surface p-4 space-y-1">
                                <h3 class="text-sm font-semibold text-ink-primary">{{ $mod['name'] }}</h3>
                                <p class="text-sm text-ink-secondary leading-relaxed">{{ $mod['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section id="workflows" class="space-y-4 scroll-mt-24">
                    <h2 class="text-[clamp(1.125rem,1.2vw+0.8rem,1.375rem)] font-semibold text-ink-primary">{{ __('docs.sections.workflows.title') }}</h2>
                    <ol class="list-decimal pl-5 space-y-2.5 text-sm sm:text-base text-ink-secondary leading-relaxed">
                        @foreach (__('docs.sections.workflows.steps') as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ol>

                    <div class="rounded-xl border border-status-approved-border bg-status-approved-bg p-4 space-y-1.5 text-status-approved-text">
                        <p class="text-sm font-semibold">{{ __('docs.sections.workflows.pin_callout_title') }}</p>
                        <p class="text-sm leading-relaxed">{{ __('docs.sections.workflows.pin_callout_body') }}</p>
                    </div>
                </section>

                <section id="matching-lifecycle" class="space-y-3 scroll-mt-24">
                    <h2 class="text-[clamp(1.125rem,1.2vw+0.8rem,1.375rem)] font-semibold text-ink-primary">{{ __('docs.sections.matching.title') }}</h2>
                    <p class="text-sm sm:text-base text-ink-secondary leading-relaxed">{{ __('docs.sections.matching.formula_intro') }}</p>
                    <ul class="list-disc pl-5 space-y-2 text-sm sm:text-base text-ink-secondary leading-relaxed">
                        @foreach (__('docs.sections.matching.weights') as $weight)
                            <li>{{ $weight }}</li>
                        @endforeach
                    </ul>
                </section>

                <section id="simulations" class="space-y-3 scroll-mt-24">
                    <h2 class="text-[clamp(1.125rem,1.2vw+0.8rem,1.375rem)] font-semibold text-ink-primary">{{ __('docs.sections.simulations.title') }}</h2>
                    <ul class="list-disc pl-5 space-y-2 text-sm sm:text-base text-ink-secondary leading-relaxed">
                        @foreach (__('docs.sections.simulations.items') as $sim)
                            <li>{{ $sim }}</li>
                        @endforeach
                    </ul>
                </section>

                <section id="customization" class="scroll-mt-24">
                    <div class="rounded-2xl border border-status-found-border bg-brand-tint p-6 space-y-3">
                        <h2 class="text-[clamp(1.125rem,1.2vw+0.8rem,1.375rem)] font-semibold text-brand">
                            {{ __('docs.sections.customization.title') }}
                        </h2>
                        <p class="text-sm sm:text-base text-ink-primary leading-relaxed">
                            {{ __('docs.sections.customization.body') }}
                        </p>
                        <p class="text-sm font-semibold text-ink-primary">
                            {{ __('docs.sections.customization.contact_label') }}
                            <a href="mailto:{{ $contactEmail }}" class="font-mono text-brand underline underline-offset-4 break-all">
                                {{ $contactEmail }}
                            </a>
                        </p>
                    </div>
                </section>
            </article>
        </div>
    </div>
@endsection
