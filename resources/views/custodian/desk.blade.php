@extends('layouts.app')

@section('title', __('app.custodian.title'))
@section('header_title', __('app.custodian.title'))

@section('content')
    <div class="space-y-1">
        <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
            {{ __('app.custodian.title') }}
        </h1>
        <p class="text-sm text-ink-secondary">{{ __('app.custodian.subtitle') }}</p>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface border border-border-subtle rounded-xl p-4 sm:p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.custodian.stats.pending_claims') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-status-pending-text tabular-nums">{{ $deskStats['pending_claims'] }}</p>
        </div>
        <div class="bg-surface border border-border-subtle rounded-xl p-4 sm:p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.custodian.stats.awaiting_handover') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-status-approved-text tabular-nums">{{ $deskStats['awaiting_handover'] }}</p>
        </div>
        <div class="bg-surface border border-border-subtle rounded-xl p-4 sm:p-5 min-w-0">
            <p class="text-xs font-medium text-ink-muted">{{ __('app.custodian.stats.deposited_items') }}</p>
            <p class="mt-2 font-mono text-2xl font-semibold text-brand tabular-nums">{{ $deskStats['deposited_items'] }}</p>
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-[clamp(1.125rem,1.1vw+0.8rem,1.375rem)] font-semibold text-ink-primary">
            {{ __('app.custodian.pending_section') }}
        </h2>

        @if ($pendingClaims->isEmpty())
            <x-ui.empty-state :title="__('app.custodian.empty_pending')" icon="shield-check" />
        @else
            <div class="space-y-4">
                @foreach ($pendingClaims as $claim)
                    <article class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-4 min-w-0" x-data>
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border-subtle pb-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-16 aspect-[4/3] rounded-lg overflow-hidden border border-border-subtle bg-surface-subtle shrink-0">
                                    <img
                                        src="{{ $claim->itemReport->displayPhotoUrl() }}"
                                        alt="{{ $claim->itemReport->title }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover {{ $claim->itemReport->is_sensitive ? 'blur-sm scale-105' : '' }}"
                                    >
                                </div>
                                <div class="flex flex-wrap items-center gap-2 min-w-0">
                                    <span class="font-mono text-xs font-semibold text-ink-primary">{{ $claim->claim_code }}</span>
                                    <span class="font-mono text-xs text-ink-muted">{{ $claim->itemReport->ticket_code }}</span>
                                    <a href="{{ route('items.show', $claim->itemReport) }}" class="text-sm font-semibold text-brand hover:underline break-words">
                                        {{ $claim->itemReport->title }}
                                    </a>
                                </div>
                            </div>
                            <span class="text-xs text-ink-secondary">{{ $claim->claimant->name }} ({{ $claim->claimant->campus_unit }})</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-xl border border-status-found-border bg-brand-tint/45 p-4 space-y-1.5 min-w-0">
                                <p class="text-xs font-semibold text-brand">{{ __('app.custodian.secret_traits_label') }}</p>
                                <p class="text-sm text-ink-primary leading-relaxed break-words">
                                    {{ $claim->itemReport->hidden_traits ?: __('app.common.none') }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-border-subtle bg-surface-subtle p-4 space-y-1.5 min-w-0">
                                <p class="text-xs font-semibold text-ink-primary">{{ __('app.custodian.claimant_answer_label') }}</p>
                                <p class="text-sm text-ink-primary leading-relaxed break-words">{{ $claim->claimant_answer }}</p>
                                @if ($claim->proofPhotoUrl())
                                    <a href="{{ $claim->proofPhotoUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-block text-xs font-medium text-brand hover:underline pt-1">
                                        {{ __('app.items.detail.proof_photo') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-border-subtle">
                            <form method="POST" action="{{ route('claims.approve', $claim) }}" class="flex-1 flex flex-col sm:flex-row gap-2 min-w-0">
                                @csrf
                                <input
                                    type="text"
                                    name="review_notes"
                                    placeholder="{{ __('app.custodian.optional_note') }}"
                                    class="flex-1 min-w-0 min-h-11 rounded-lg border border-border-strong bg-surface px-3 text-sm text-ink-primary placeholder:text-ink-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                                >
                                <x-ui.button type="submit" variant="primary" size="md">
                                    {{ __('app.actions.approve_claim') }}
                                </x-ui.button>
                            </form>

                            <x-ui.button type="button" variant="danger" size="md" @click="$dispatch('open-modal', 'reject-claim-{{ $claim->id }}')">
                                {{ __('app.actions.reject_claim') }}
                            </x-ui.button>
                        </div>

                        <x-ui.modal :name="'reject-claim-'.$claim->id" :title="__('app.actions.reject_claim').' · '.$claim->claim_code">
                            <form method="POST" action="{{ route('claims.reject', $claim) }}" class="space-y-4">
                                @csrf
                                <x-ui.textarea
                                    name="review_notes"
                                    :label="__('app.custodian.reject_reason')"
                                    rows="3"
                                    required
                                />
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-ui.button type="button" variant="secondary" @click="$dispatch('close-modal', 'reject-claim-{{ $claim->id }}')">
                                        {{ __('app.common.cancel') }}
                                    </x-ui.button>
                                    <x-ui.button type="submit" variant="danger">
                                        {{ __('app.actions.reject_claim') }}
                                    </x-ui.button>
                                </div>
                            </form>
                        </x-ui.modal>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="space-y-4">
        <h2 class="text-[clamp(1.125rem,1.1vw+0.8rem,1.375rem)] font-semibold text-ink-primary">
            {{ __('app.custodian.handover_section') }}
        </h2>

        @if ($approvedClaims->isEmpty())
            <x-ui.empty-state :title="__('app.custodian.empty_handover')" icon="key-round" />
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($approvedClaims as $claim)
                    <article class="bg-surface border border-status-approved-border rounded-xl p-5 space-y-4 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-mono text-xs font-semibold text-status-approved-text">{{ $claim->claim_code }}</span>
                            <span class="font-mono text-xs text-ink-muted">{{ $claim->itemReport->ticket_code }}</span>
                        </div>
                        <div class="space-y-1 min-w-0">
                            <a href="{{ route('items.show', $claim->itemReport) }}" class="block text-base font-semibold text-ink-primary hover:text-brand break-words">
                                {{ $claim->itemReport->title }}
                            </a>
                            <p class="text-xs text-ink-secondary">
                                {{ $claim->claimant->name }} ·
                                {{ $claim->itemReport->custodyPost ? $claim->itemReport->custodyPost->localizedName() : $claim->itemReport->custody_type?->label() }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('claims.handover', $claim) }}" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input
                                type="text"
                                name="handover_pin"
                                placeholder="{{ __('app.custodian.pin_placeholder') }}"
                                aria-label="{{ __('app.custodian.enter_pin') }}"
                                required
                                class="flex-1 min-w-0 min-h-11 rounded-lg border border-border-strong bg-surface px-3 font-mono text-sm text-center tracking-[0.25em] text-ink-primary placeholder:tracking-normal placeholder:font-sans placeholder:text-ink-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                            >
                            <x-ui.button type="submit" variant="primary" size="md">
                                {{ __('app.actions.verify_pin') }}
                            </x-ui.button>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="space-y-4">
        <h2 class="text-[clamp(1.125rem,1.1vw+0.8rem,1.375rem)] font-semibold text-ink-primary">
            {{ __('app.custodian.deposited_section') }}
        </h2>

        @if ($depositedItems->isEmpty())
            <x-ui.empty-state :title="__('app.custodian.empty_deposited')" icon="archive" />
        @else
            <div class="md:hidden space-y-3">
                @foreach ($depositedItems as $item)
                    <div class="bg-surface border border-border-subtle rounded-xl p-4 space-y-2.5 min-w-0" x-data>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-mono text-xs text-ink-muted">{{ $item->ticket_code }}</span>
                            <x-ui.badge tone="found">{{ $item->campusZone->code }}</x-ui.badge>
                        </div>
                        <a href="{{ route('items.show', $item) }}" class="block text-sm font-semibold text-ink-primary break-words">{{ $item->title }}</a>
                        <p class="text-xs text-ink-secondary">{{ $item->custodyPost?->localizedName() }}</p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <x-ui.button :href="route('items.show', $item)" variant="secondary" size="sm" class="flex-1">{{ __('app.common.view_detail') }}</x-ui.button>
                            <x-ui.button type="button" variant="ghost" size="sm" @click="$dispatch('open-modal', 'archive-desk-{{ $item->id }}')">{{ __('app.actions.archive_report') }}</x-ui.button>
                        </div>
                        <x-ui.confirm-action
                            :modalName="'archive-desk-'.$item->id"
                            :title="__('app.items.detail.archive_modal_title', ['code' => $item->ticket_code])"
                            :message="__('app.items.detail.archive_modal_body')"
                            :actionUrl="route('items.archive', $item)"
                            :confirmLabel="__('app.actions.archive_report')"
                        />
                    </div>
                @endforeach
            </div>

            <div class="hidden md:block bg-surface border border-border-subtle rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-subtle text-xs font-semibold text-ink-secondary border-b border-border-subtle">
                        <tr>
                            <th class="px-4 py-3">{{ __('app.items.detail.ticket_code') }}</th>
                            <th class="px-4 py-3">{{ __('app.items.fields.title') }}</th>
                            <th class="px-4 py-3">{{ __('app.items.fields.custody_post_id') }}</th>
                            <th class="px-4 py-3">{{ __('app.items.detail.reported_at') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('app.actions.review') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @foreach ($depositedItems as $item)
                            <tr class="hover:bg-canvas/50" x-data>
                                <td class="px-4 py-3 font-mono text-xs text-ink-muted whitespace-nowrap">{{ $item->ticket_code }}</td>
                                <td class="px-4 py-3 font-medium text-ink-primary max-w-xs truncate">
                                    <a href="{{ route('items.show', $item) }}" class="hover:text-brand">{{ $item->title }}</a>
                                </td>
                                <td class="px-4 py-3 text-xs text-ink-secondary">{{ $item->custodyPost?->localizedName() }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-ink-muted whitespace-nowrap">{{ $item->incident_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <x-ui.button :href="route('items.show', $item)" variant="secondary" size="sm">{{ __('app.common.view_detail') }}</x-ui.button>
                                        <x-ui.button type="button" variant="ghost" size="sm" @click="$dispatch('open-modal', 'archive-desk-dt-{{ $item->id }}')">{{ __('app.actions.archive_report') }}</x-ui.button>
                                    </div>
                                    <x-ui.confirm-action
                                        :modalName="'archive-desk-dt-'.$item->id"
                                        :title="__('app.items.detail.archive_modal_title', ['code' => $item->ticket_code])"
                                        :message="__('app.items.detail.archive_modal_body')"
                                        :actionUrl="route('items.archive', $item)"
                                        :confirmLabel="__('app.actions.archive_report')"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
