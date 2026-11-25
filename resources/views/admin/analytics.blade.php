@extends('layouts.app')

@section('title', __('app.admin.title'))
@section('header_title', __('app.admin.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-0 space-y-1">
            <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
                {{ __('app.admin.title') }}
            </h1>
            <p class="text-sm text-ink-secondary">{{ __('app.admin.subtitle') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('admin.master.index')" variant="secondary" size="sm">
                <x-ui.icon name="settings" class="w-4 h-4" />
                <span>{{ __('app.nav.admin_master') }}</span>
            </x-ui.button>
            <x-ui.button :href="route('admin.export.items')" variant="primary" size="sm">
                <x-ui.icon name="download" class="w-4 h-4" />
                <span>{{ __('app.actions.export_csv') }}</span>
            </x-ui.button>
        </div>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-surface border border-border-subtle rounded-xl p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.admin.kpis.total_reports') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-ink-primary tabular-nums">{{ $kpis['total_reports'] }}</p>
            <p class="mt-1 text-xs text-ink-secondary">{{ __('app.admin.kpis.lost_found_split', ['lost' => $kpis['lost_count'], 'found' => $kpis['found_count']]) }}</p>
        </div>

        <div class="bg-surface border border-border-subtle rounded-xl p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.admin.kpis.recovery_rate') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-brand tabular-nums">{{ $kpis['recovery_rate'] }}%</p>
            <p class="mt-1 text-[11px] text-ink-muted">{{ __('app.admin.kpis.recovery_rate_note') }}</p>
        </div>

        <div class="bg-surface border border-border-subtle rounded-xl p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.admin.kpis.avg_recovery_hours') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-ink-primary tabular-nums">
                {{ $kpis['avg_recovery_hours'] }} <span class="text-sm font-normal text-ink-secondary">{{ __('app.common.hours_short') }}</span>
            </p>
        </div>

        <div class="bg-surface border border-border-subtle rounded-xl p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.admin.kpis.pending_claims') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-status-pending-text tabular-nums">{{ $kpis['pending_claims'] }}</p>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="bg-surface border border-border-subtle rounded-xl p-5 space-y-4 min-w-0">
            <h2 class="text-base font-semibold text-ink-primary">{{ __('app.admin.hotspots_title') }}</h2>
            @php $maxLost = max(1, (int) $hotspots->max('lost_count')); @endphp
            <ul class="space-y-3" role="list">
                @foreach ($hotspots as $zone)
                    @php $pct = (int) round(($zone->lost_count / $maxLost) * 100); @endphp
                    <li class="space-y-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <span class="font-medium text-ink-primary truncate">
                                <span class="font-mono text-ink-muted">{{ $zone->code }}</span> · {{ $zone->localizedName() }}
                            </span>
                            <span class="font-mono text-ink-secondary tabular-nums shrink-0">{{ __('app.admin.lost_count_label', ['count' => $zone->lost_count]) }}</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-surface-subtle overflow-hidden">
                            <div class="h-full rounded-full bg-signal" style="width: {{ $pct }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="bg-surface border border-border-subtle rounded-xl p-5 space-y-4 min-w-0">
            <h2 class="text-base font-semibold text-ink-primary">{{ __('app.admin.status_breakdown_title') }}</h2>
            <ul class="space-y-3" role="list">
                @foreach ($statusBreakdown as $row)
                    <li class="space-y-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <x-ui.badge :tone="$row['status']->badgeTone()">{{ $row['status']->label() }}</x-ui.badge>
                            <span class="font-mono text-ink-secondary tabular-nums">{{ $row['count'] }} ({{ $row['percentage'] }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-surface-subtle overflow-hidden">
                            <div class="h-full rounded-full bg-brand" style="width: {{ $row['percentage'] }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <section class="space-y-3 min-w-0">
        <h2 class="text-base font-semibold text-ink-primary">{{ __('app.admin.ledger_title') }}</h2>

        <div class="w-full max-w-full overflow-x-auto overscroll-x-contain rounded-xl border border-border-subtle bg-surface">
            <table class="w-full text-left text-sm min-w-[44rem]">
                <thead class="bg-surface-subtle text-xs font-semibold text-ink-secondary border-b border-border-subtle">
                    <tr>
                        <th class="px-4 py-3">{{ __('app.items.detail.ticket_code') }}</th>
                        <th class="px-4 py-3">{{ __('app.items.fields.type') }}</th>
                        <th class="px-4 py-3">{{ __('app.items.fields.title') }}</th>
                        <th class="px-4 py-3">{{ __('app.items.fields.campus_zone_id') }}</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">{{ __('app.items.detail.reported_at') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('app.actions.review') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @foreach ($recentItems as $item)
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3 font-mono text-xs text-ink-muted whitespace-nowrap">{{ $item->ticket_code }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-ui.badge :tone="$item->type->badgeTone()">{{ $item->type->label() }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 font-medium text-ink-primary max-w-xs truncate">
                                <a href="{{ route('items.show', $item) }}" class="hover:text-brand">{{ $item->title }}</a>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-ink-secondary whitespace-nowrap">{{ $item->campusZone->code }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-ui.badge :tone="$item->status->badgeTone()">{{ $item->status->label() }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-ink-muted whitespace-nowrap">{{ $item->incident_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if ($item->status === \App\Enums\ItemStatus::Open)
                                    <form method="POST" action="{{ route('items.archive', $item) }}" class="inline">
                                        @csrf
                                        <x-ui.button type="submit" variant="ghost" size="sm">{{ __('app.actions.archive_report') }}</x-ui.button>
                                    </form>
                                @else
                                    <a href="{{ route('items.show', $item) }}" class="text-xs font-medium text-brand hover:underline">{{ __('app.common.view_detail') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
