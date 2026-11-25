@extends('layouts.app')

@section('title', __('app.dashboard.title'))
@section('header_title', __('app.dashboard.title'))

@section('content')
    <section class="flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-0 space-y-1">
            <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] leading-[1.2] tracking-[-0.02em] font-semibold text-ink-primary break-words">
                {{ __('app.dashboard.welcome', ['name' => auth()->user()->name]) }}
            </h1>
            <p class="text-sm text-ink-secondary">{{ __('app.dashboard.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('items.create', ['type' => 'lost'])" variant="signal" size="sm">
                <x-ui.icon name="plus" class="w-4 h-4" />
                <span>{{ __('app.actions.report_lost') }}</span>
            </x-ui.button>
            <x-ui.button :href="route('items.create', ['type' => 'found'])" variant="primary" size="sm">
                <x-ui.icon name="plus" class="w-4 h-4" />
                <span>{{ __('app.actions.report_found') }}</span>
            </x-ui.button>
        </div>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-surface border border-border-subtle rounded-xl p-4 sm:p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.dashboard.stats.open_reports') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-ink-primary tabular-nums">{{ $summaryStats['open_reports'] }}</p>
        </div>
        <div class="bg-surface border border-border-subtle rounded-xl p-4 sm:p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.dashboard.stats.active_matches') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-brand tabular-nums">{{ $summaryStats['active_matches'] }}</p>
        </div>
        <div class="bg-surface border border-border-subtle rounded-xl p-4 sm:p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.dashboard.stats.active_claims') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-ink-primary tabular-nums">{{ $summaryStats['active_claims'] }}</p>
        </div>
        <div class="bg-surface border border-border-subtle rounded-xl p-4 sm:p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.dashboard.stats.returned_items') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-ink-primary tabular-nums">{{ $summaryStats['returned_items'] }}</p>
        </div>
    </section>

    @if ($approvedClaims->isNotEmpty())
        <section class="space-y-3">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[clamp(1.125rem,1.1vw+0.8rem,1.375rem)] font-semibold text-ink-primary">
                    {{ __('app.dashboard.pin_ready_title') }}
                </h2>
            </div>
            <p class="text-sm text-ink-secondary">{{ __('app.dashboard.pin_ready_desc') }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($approvedClaims as $claim)
                    <div class="rounded-xl border border-status-approved-border bg-status-approved-bg/60 p-5 space-y-3 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-mono text-xs font-semibold text-status-approved-text">{{ $claim->claim_code }}</span>
                            <x-ui.badge tone="approved">{{ $claim->status->label() }}</x-ui.badge>
                        </div>
                        <a href="{{ route('items.show', $claim->itemReport) }}" class="block text-base font-semibold text-ink-primary hover:text-brand break-words">
                            {{ $claim->itemReport->title }}
                        </a>
                        <div class="rounded-lg bg-surface border border-status-approved-border p-3 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-medium text-ink-muted">{{ __('app.dashboard.pin_label') }}</p>
                                <p class="font-mono text-2xl font-bold tracking-[0.2em] text-status-approved-text tabular-nums">
                                    {{ $claim->formattedPin() }}
                                </p>
                            </div>
                            <div class="text-xs text-ink-secondary min-w-0">
                                <p class="font-medium text-ink-primary">{{ __('app.dashboard.pickup_at') }}</p>
                                @if ($claim->itemReport->custodyPost)
                                    <p class="break-words">{{ $claim->itemReport->custodyPost->localizedName() }} ({{ $claim->itemReport->custodyPost->operating_hours }})</p>
                                @else
                                    <p class="break-words">{{ __('enums.custody_type.with_finder') }} ({{ $claim->itemReport->reporter->name }})</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <section class="bg-surface border border-border-subtle rounded-xl p-5 space-y-4 min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-ink-primary">{{ __('app.dashboard.suggested_matches_title') }}</h2>
                <a href="{{ route('matches.index') }}" class="text-xs font-medium text-brand hover:underline">{{ __('app.dashboard.see_all_matches') }}</a>
            </div>

            @if ($topMatches->isEmpty())
                <p class="text-sm text-ink-secondary">{{ __('app.dashboard.no_matches') }}</p>
            @else
                <div class="divide-y divide-border-subtle">
                    @foreach ($topMatches as $match)
                        <div class="py-3 first:pt-0 last:pb-0 space-y-1.5 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <x-ui.badge tone="brand" mono>{{ __('app.matches.score_label', ['score' => $match->score]) }}</x-ui.badge>
                                <span class="font-mono text-xs text-ink-muted">{{ $match->foundItem->campusZone->code }}</span>
                            </div>
                            <a href="{{ route('items.show', $match->foundItem) }}" class="block text-sm font-semibold text-ink-primary hover:text-brand break-words">
                                {{ $match->foundItem->title }}
                            </a>
                            <p class="text-xs text-ink-muted break-words">
                                {{ $match->lostItem->ticket_code }} ↔ {{ $match->foundItem->ticket_code }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="bg-surface border border-border-subtle rounded-xl p-5 space-y-4 min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-ink-primary">{{ __('app.dashboard.my_reports_title') }}</h2>
                <a href="{{ route('items.index') }}" class="text-xs font-medium text-brand hover:underline">{{ __('app.dashboard.see_all_catalog') }}</a>
            </div>

            @if ($myReports->isEmpty())
                <p class="text-sm text-ink-secondary">{{ __('app.dashboard.no_reports') }}</p>
            @else
                <div class="divide-y divide-border-subtle">
                    @foreach ($myReports as $report)
                        <div class="py-3 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-2 min-w-0">
                            <div class="min-w-0 space-y-0.5">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-mono text-xs text-ink-muted">{{ $report->ticket_code }}</span>
                                    <x-ui.badge :tone="$report->type->badgeTone()">{{ $report->type->label() }}</x-ui.badge>
                                </div>
                                <a href="{{ route('items.show', $report) }}" class="block text-sm font-medium text-ink-primary hover:text-brand break-words">
                                    {{ $report->title }}
                                </a>
                            </div>
                            <x-ui.badge :tone="$report->status->badgeTone()">{{ $report->status->label() }}</x-ui.badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
