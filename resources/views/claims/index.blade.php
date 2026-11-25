@extends('layouts.app')

@section('title', __('app.claims.title'))
@section('header_title', __('app.claims.title'))

@section('content')
    <div class="space-y-1">
        <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
            {{ __('app.claims.title') }}
        </h1>
        <p class="text-sm text-ink-secondary">{{ __('app.claims.subtitle') }}</p>
    </div>

    @if ($claims->isEmpty())
        <x-ui.empty-state
            :title="__('app.claims.empty_title')"
            :description="__('app.claims.empty_desc')"
            icon="clipboard-check"
            :actionLabel="__('app.nav.catalog')"
            :actionHref="route('items.index', ['type' => 'found'])"
        />
    @else
        <div class="space-y-4">
            @foreach ($claims as $claim)
                <article class="bg-surface border border-border-subtle rounded-xl p-5 space-y-3 min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-ink-primary">{{ $claim->claim_code }}</span>
                            <span class="font-mono text-xs text-ink-muted">{{ $claim->itemReport->ticket_code }}</span>
                        </div>
                        <x-ui.badge :tone="$claim->status->badgeTone()">{{ $claim->status->label() }}</x-ui.badge>
                    </div>

                    <a href="{{ route('items.show', $claim->itemReport) }}" class="block text-base font-semibold text-ink-primary hover:text-brand break-words">
                        {{ $claim->itemReport->title }}
                    </a>

                    <p class="text-sm text-ink-secondary break-words">
                        <span class="font-medium text-ink-primary">{{ __('app.claims.your_answer') }}:</span> {{ $claim->claimant_answer }}
                    </p>

                    @if ($claim->status === \App\Enums\ClaimStatus::Approved && $claim->claimant_id === auth()->id())
                        <div class="rounded-xl border border-status-approved-border bg-status-approved-bg p-4 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-medium text-status-approved-text">{{ __('app.dashboard.pin_label') }}</p>
                                <p class="font-mono text-2xl font-bold tracking-[0.2em] text-status-approved-text tabular-nums">
                                    {{ $claim->formattedPin() }}
                                </p>
                            </div>
                            <div class="text-xs text-ink-secondary min-w-0">
                                <p class="font-semibold text-ink-primary">{{ __('app.dashboard.pickup_at') }}</p>
                                @if ($claim->itemReport->custodyPost)
                                    <p class="break-words">{{ $claim->itemReport->custodyPost->localizedName() }} ({{ $claim->itemReport->custodyPost->operating_hours }})</p>
                                @else
                                    <p class="break-words">{{ __('enums.custody_type.with_finder') }} ({{ $claim->itemReport->reporter->name }})</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($claim->review_notes)
                        <p class="text-xs text-ink-muted border-t border-border-subtle pt-2 break-words">
                            {{ __('app.claims.reviewer_note') }}: {{ $claim->review_notes }}
                        </p>
                    @endif
                </article>
            @endforeach
        </div>

        <div>
            {{ $claims->links() }}
        </div>
    @endif
@endsection
