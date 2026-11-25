@extends('layouts.app')

@section('title', $item->ticket_code.' — '.$item->title)
@section('header_title', $item->ticket_code)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink-secondary hover:text-brand">
            <x-ui.icon name="arrow-left" class="w-4 h-4" />
            <span>{{ __('app.nav.catalog') }}</span>
        </a>

        <div
            class="flex flex-wrap items-center gap-2"
            x-data="{ copied: false, refText: @js('#'.$item->ticket_code.' | '.$item->title.' | '.$item->campusZone->localizedName().' | '.route('items.show', $item)) }"
        >
            <x-ui.button
                type="button"
                variant="secondary"
                size="sm"
                @click="navigator.clipboard?.writeText(refText); copied = true; setTimeout(() => copied = false, 2000)"
            >
                <x-ui.icon name="copy" class="w-3.5 h-3.5" />
                <span x-text="copied ? @js(__('app.actions.reference_copied')) : @js(__('app.actions.copy_reference'))"></span>
            </x-ui.button>

            <x-ui.button type="button" variant="secondary" size="sm" @click="window.print()">
                <x-ui.icon name="printer" class="w-3.5 h-3.5" />
                <span>{{ __('app.actions.print_card') }}</span>
            </x-ui.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_22rem] gap-6 items-start">
        <div class="min-w-0 space-y-6">
            <article class="bg-surface border border-border-subtle rounded-2xl overflow-hidden">
                <x-ui.item-thumbnail :item="$item" :canReveal="$canViewSensitive" :allowToggle="$canViewSensitive" />

                <div class="p-5 sm:p-6 space-y-5 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-sm font-semibold text-ink-primary">{{ $item->ticket_code }}</span>
                        <x-ui.badge :tone="$item->type->badgeTone()">{{ $item->type->label() }}</x-ui.badge>
                        <x-ui.badge :tone="$item->status->badgeTone()">{{ $item->status->label() }}</x-ui.badge>
                        <x-ui.badge tone="neutral">{{ $item->category->label() }}</x-ui.badge>
                    </div>

                    <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] leading-[1.2] font-semibold text-ink-primary break-words">
                        {{ $item->title }}
                    </h1>

                    <p class="text-sm sm:text-base text-ink-secondary leading-relaxed break-words">
                        {{ $item->description }}
                    </p>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-border-subtle text-sm">
                        <div class="min-w-0">
                            <dt class="text-xs text-ink-muted">{{ __('app.items.detail.location') }}</dt>
                            <dd class="mt-0.5 font-medium text-ink-primary break-words">
                                <span class="font-mono text-xs text-brand">{{ $item->campusZone->code }}</span> · {{ $item->campusZone->localizedName() }}
                                @if ($item->location_detail)
                                    <span class="block text-xs text-ink-secondary font-normal">{{ $item->location_detail }}</span>
                                @endif
                            </dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs text-ink-muted">{{ __('app.items.detail.reported_at') }}</dt>
                            <dd class="mt-0.5 font-mono text-ink-primary">{{ $item->incident_date->format('Y-m-d') }}</dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs text-ink-muted">{{ __('app.items.fields.brand') }} / {{ __('app.items.fields.color') }}</dt>
                            <dd class="mt-0.5 text-ink-primary break-words">
                                {{ $item->brand ?: __('app.common.none') }} · {{ $item->color ?: __('app.common.none') }}
                            </dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs text-ink-muted">{{ __('app.items.detail.reported_by') }}</dt>
                            <dd class="mt-0.5 text-ink-primary break-words">
                                {{ $item->reporter->name }}
                                <span class="block text-xs text-ink-muted">{{ $item->reporter->campus_unit }}</span>
                            </dd>
                        </div>
                    </dl>

                    @if ($item->type === \App\Enums\ItemType::Found && $item->custody_type !== null)
                        <div class="rounded-xl border border-border-subtle bg-surface-subtle p-4 space-y-1">
                            <p class="text-xs font-medium text-ink-muted">{{ __('app.items.detail.custody') }}</p>
                            <p class="text-sm font-semibold text-ink-primary">{{ $item->custody_type->label() }}</p>
                            @if ($item->custodyPost)
                                <p class="text-xs text-ink-secondary break-words">
                                    {{ $item->custodyPost->localizedName() }} · {{ $item->custodyPost->operating_hours }}
                                    @if ($item->custodyPost->contact_ext)
                                        ({{ $item->custodyPost->contact_ext }})
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endif

                    @if ($canViewHidden && $item->hidden_traits)
                        <div class="rounded-xl border border-status-found-border bg-brand-tint/50 p-4 space-y-1.5">
                            <div class="flex items-center gap-2 text-brand">
                                <x-ui.icon name="lock" class="w-4 h-4 shrink-0" />
                                <p class="text-xs font-semibold">{{ __('app.privacy.hidden_traits_title') }}</p>
                            </div>
                            <p class="text-sm text-ink-primary leading-relaxed break-words">{{ $item->hidden_traits }}</p>
                        </div>
                    @endif
                </div>
            </article>

            @if ($relatedMatches->isNotEmpty())
                <section class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-3 no-print">
                    <h2 class="text-base font-semibold text-ink-primary">{{ __('app.items.detail.related_matches') }}</h2>
                    <div class="divide-y divide-border-subtle">
                        @foreach ($relatedMatches as $match)
                            @php $counterpart = $item->type === \App\Enums\ItemType::Lost ? $match->foundItem : $match->lostItem; @endphp
                            <div class="py-3 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-ui.badge tone="brand" mono>{{ __('app.matches.score_label', ['score' => $match->score]) }}</x-ui.badge>
                                        <span class="font-mono text-xs text-ink-muted">{{ $counterpart->ticket_code }}</span>
                                    </div>
                                    <a href="{{ route('items.show', $counterpart) }}" class="mt-1 block text-sm font-medium text-ink-primary hover:text-brand break-words">
                                        {{ $counterpart->title }}
                                    </a>
                                </div>
                                <x-ui.button :href="route('items.show', $counterpart)" variant="secondary" size="sm">
                                    {{ __('app.common.view_detail') }}
                                </x-ui.button>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="min-w-0 space-y-4 lg:sticky lg:top-24 no-print">
            @if ($userClaim)
                <div class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-ink-primary">{{ __('app.items.detail.your_claim_title') }}</h2>
                        <x-ui.badge :tone="$userClaim->status->badgeTone()">{{ $userClaim->status->label() }}</x-ui.badge>
                    </div>
                    <p class="font-mono text-xs text-ink-muted">{{ $userClaim->claim_code }}</p>
                    <p class="text-xs text-ink-secondary break-words">{{ $userClaim->claimant_answer }}</p>

                    @if ($userClaim->status === \App\Enums\ClaimStatus::Approved && $userClaim->claimant_id === auth()->id())
                        <div class="rounded-xl border border-status-approved-border bg-status-approved-bg p-4 text-center space-y-1">
                            <p class="text-xs font-medium text-status-approved-text">{{ __('app.dashboard.pin_label') }}</p>
                            <p class="font-mono text-2xl font-bold tracking-[0.2em] text-status-approved-text tabular-nums">
                                {{ $userClaim->formattedPin() }}
                            </p>
                        </div>
                    @endif

                    @if ($userClaim->review_notes)
                        <p class="text-xs text-ink-muted border-t border-border-subtle pt-2 break-words">
                            {{ __('app.claims.reviewer_note') }}: {{ $userClaim->review_notes }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($canSubmitClaim)
                <div class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-4">
                    <div class="space-y-1">
                        <h2 class="text-base font-semibold text-ink-primary">{{ __('app.items.detail.claim_box_title') }}</h2>
                        <p class="text-xs text-ink-secondary leading-relaxed">{{ __('app.items.detail.claim_box_desc') }}</p>
                    </div>

                    <form method="POST" action="{{ route('claims.store', $item) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <x-ui.textarea
                            name="claimant_answer"
                            :label="__('app.items.detail.claimant_answer')"
                            :placeholder="__('app.items.detail.claimant_answer_placeholder')"
                            rows="4"
                            required
                        />
                        <x-ui.input
                            name="proof_photo"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp"
                            :label="__('app.items.detail.proof_photo')"
                        />
                        <x-ui.button type="submit" variant="primary" class="w-full">
                            {{ __('app.actions.submit_claim') }}
                        </x-ui.button>
                    </form>
                </div>
            @endif

            @if ($canHandover && $activeApprovedClaim)
                <div class="bg-surface border border-status-approved-border rounded-2xl p-5 space-y-4">
                    <div class="space-y-1">
                        <h2 class="text-base font-semibold text-ink-primary">{{ __('app.items.detail.finder_handover_title') }}</h2>
                        <p class="text-xs text-ink-secondary leading-relaxed">{{ __('app.items.detail.finder_handover_desc') }}</p>
                    </div>
                    <p class="text-xs text-ink-muted">
                        {{ $activeApprovedClaim->claim_code }} · {{ $activeApprovedClaim->claimant->name }}
                    </p>
                    <form method="POST" action="{{ route('claims.handover', $activeApprovedClaim) }}" class="space-y-3">
                        @csrf
                        <x-ui.input
                            name="handover_pin"
                            :label="__('app.custodian.enter_pin')"
                            :placeholder="__('app.custodian.pin_placeholder')"
                            class="font-mono text-center tracking-[0.25em]"
                            required
                        />
                        <x-ui.button type="submit" variant="primary" class="w-full">
                            {{ __('app.actions.verify_pin') }}
                        </x-ui.button>
                    </form>
                </div>
            @endif

            @if (auth()->user()->can('update', $item) || auth()->user()->can('cancel', $item) || auth()->user()->can('archive', $item))
                <div class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-2.5" x-data>
                    @can('update', $item)
                        <x-ui.button :href="route('items.edit', $item)" variant="secondary" class="w-full">
                            <x-ui.icon name="edit" class="w-4 h-4" />
                            <span>{{ __('app.actions.update_report') }}</span>
                        </x-ui.button>
                    @endcan

                    @can('cancel', $item)
                        <x-ui.button type="button" variant="danger" class="w-full" @click="$dispatch('open-modal', 'cancel-item-modal')">
                            {{ __('app.actions.cancel_report') }}
                        </x-ui.button>

                        <x-ui.confirm-action
                            modalName="cancel-item-modal"
                            :title="__('app.items.detail.cancel_modal_title', ['code' => $item->ticket_code])"
                            :message="__('app.items.detail.cancel_modal_body')"
                            :actionUrl="route('items.cancel', $item)"
                            :confirmLabel="__('app.actions.cancel_report')"
                            confirmVariant="danger"
                        />
                    @endcan

                    @can('archive', $item)
                        <x-ui.button type="button" variant="secondary" class="w-full" @click="$dispatch('open-modal', 'archive-item-modal')">
                            <x-ui.icon name="archive" class="w-4 h-4" />
                            <span>{{ __('app.actions.archive_report') }}</span>
                        </x-ui.button>

                        <x-ui.confirm-action
                            modalName="archive-item-modal"
                            :title="__('app.items.detail.archive_modal_title', ['code' => $item->ticket_code])"
                            :message="__('app.items.detail.archive_modal_body')"
                            :actionUrl="route('items.archive', $item)"
                            :confirmLabel="__('app.actions.archive_report')"
                            confirmVariant="primary"
                        />
                    @endcan
                </div>
            @endif
        </aside>
    </div>
@endsection
