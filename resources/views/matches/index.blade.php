@extends('layouts.app')

@section('title', __('app.matches.title'))
@section('header_title', __('app.matches.title'))

@section('content')
    <div class="space-y-1">
        <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
            {{ __('app.matches.title') }}
        </h1>
        <p class="text-sm text-ink-secondary max-w-3xl">{{ __('app.matches.subtitle') }}</p>
    </div>

    @if ($matches->isEmpty())
        <x-ui.empty-state
            :title="__('app.matches.empty_title')"
            :description="__('app.matches.empty_desc')"
            icon="sparkles"
            :actionLabel="__('app.nav.catalog')"
            :actionHref="route('items.index')"
        />
    @else
        <div class="space-y-4">
            @foreach ($matches as $match)
                @php $reasons = $match->match_reasons ?? []; @endphp
                <article class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-4 min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border-subtle pb-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.badge tone="brand" mono>{{ __('app.matches.score_label', ['score' => $match->score]) }}</x-ui.badge>
                            <span class="text-xs font-mono text-ink-muted">{{ __('app.matches.breakdown.category', ['pts' => $reasons['category'] ?? 0]) }}</span>
                            <span class="text-xs font-mono text-ink-muted">{{ __('app.matches.breakdown.zone', ['pts' => $reasons['zone'] ?? 0]) }}</span>
                            <span class="text-xs font-mono text-ink-muted">{{ __('app.matches.breakdown.keywords', ['pts' => $reasons['keywords'] ?? 0]) }}</span>
                            <span class="text-xs font-mono text-ink-muted">{{ __('app.matches.breakdown.date', ['pts' => $reasons['date'] ?? 0]) }}</span>
                        </div>

                        <form method="POST" action="{{ route('matches.dismiss', $match) }}">
                            @csrf
                            <x-ui.button type="submit" variant="ghost" size="sm">
                                {{ __('app.actions.dismiss_match') }}
                            </x-ui.button>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-status-lost-border bg-status-lost-bg/40 p-4 flex items-start gap-3 min-w-0">
                            <div class="w-20 aspect-[4/3] rounded-lg overflow-hidden border border-border-subtle bg-surface-subtle shrink-0">
                                <img
                                    src="{{ $match->lostItem->displayPhotoUrl() }}"
                                    alt="{{ $match->lostItem->title }}"
                                    loading="lazy"
                                    class="w-full h-full object-cover {{ $match->lostItem->is_sensitive ? 'blur-sm scale-105' : '' }}"
                                >
                            </div>
                            <div class="space-y-1.5 min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-xs font-semibold text-status-lost-text">{{ __('app.matches.lost_side') }}</span>
                                    <span class="font-mono text-xs text-ink-muted">{{ $match->lostItem->ticket_code }}</span>
                                </div>
                                <a href="{{ route('items.show', $match->lostItem) }}" class="block text-sm sm:text-base font-semibold text-ink-primary hover:text-brand break-words">
                                    {{ $match->lostItem->title }}
                                </a>
                                <p class="text-xs text-ink-secondary break-words">
                                    {{ $match->lostItem->campusZone->code }} · {{ $match->lostItem->campusZone->localizedName() }} · {{ $match->lostItem->incident_date->format('Y-m-d') }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-status-found-border bg-brand-tint/40 p-4 space-y-3 min-w-0 flex flex-col justify-between">
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="w-20 aspect-[4/3] rounded-lg overflow-hidden border border-border-subtle bg-surface-subtle shrink-0">
                                    <img
                                        src="{{ $match->foundItem->displayPhotoUrl() }}"
                                        alt="{{ $match->foundItem->title }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover {{ $match->foundItem->is_sensitive ? 'blur-sm scale-105' : '' }}"
                                    >
                                </div>
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-xs font-semibold text-brand">{{ __('app.matches.found_side') }}</span>
                                        <span class="font-mono text-xs text-ink-muted">{{ $match->foundItem->ticket_code }}</span>
                                    </div>
                                    <a href="{{ route('items.show', $match->foundItem) }}" class="block text-sm sm:text-base font-semibold text-ink-primary hover:text-brand break-words">
                                        {{ $match->foundItem->title }}
                                    </a>
                                    <p class="text-xs text-ink-secondary break-words">
                                        {{ $match->foundItem->campusZone->code }} · {{ $match->foundItem->campusZone->localizedName() }} · {{ $match->foundItem->incident_date->format('Y-m-d') }}
                                    </p>
                                </div>
                            </div>

                            <div class="pt-1">
                                <x-ui.button :href="route('items.show', $match->foundItem)" size="sm" variant="primary">
                                    {{ __('app.actions.submit_claim') }}
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div>
            {{ $matches->links() }}
        </div>
    @endif
@endsection
