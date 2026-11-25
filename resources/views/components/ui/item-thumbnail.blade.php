@props([
    'item',
    'canReveal' => false,
    'allowToggle' => false,
])

@php
    $isSensitive = (bool) $item->is_sensitive;
    $hasRealPhoto = $item->hasUploadedOrSeededPhoto();
    $photoUrl = $item->displayPhotoUrl();
    $zoneCode = $item->campusZone?->code ?? 'CAMPUS';
@endphp

<div
    @if ($isSensitive) x-data="{ revealed: false }" @endif
    {{ $attributes->merge(['class' => 'aspect-[4/3] w-full relative overflow-hidden bg-surface-subtle border-b border-border-subtle']) }}
>
    <div
        @if ($isSensitive)
            :class="revealed ? '' : 'blur-md scale-105 pointer-events-none select-none'"
        @endif
        class="w-full h-full transition-all duration-200"
    >
        <img
            src="{{ $photoUrl }}"
            alt="{{ $item->title }}"
            loading="lazy"
            class="w-full h-full object-cover"
        >
    </div>

    <div class="absolute bottom-2.5 left-2.5 z-10 pointer-events-none">
        <span class="inline-flex items-center rounded-md bg-surface/95 border border-border-subtle px-2 py-0.5 font-mono text-[11px] font-medium text-ink-primary">
            {{ $zoneCode }}
        </span>
    </div>

    @if (! $hasRealPhoto)
        <div class="absolute bottom-2.5 right-2.5 z-10 pointer-events-none">
            <span class="inline-flex items-center rounded-md bg-surface/90 border border-border-subtle px-2 py-0.5 text-[11px] font-medium text-ink-secondary">
                {{ __('app.items.category_illustration') }}
            </span>
        </div>
    @endif

    @if ($isSensitive)
        <div
            x-show="!revealed"
            class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-3 bg-ink-primary/25 text-center"
        >
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-ink-primary/85 text-white px-3 py-1.5 text-xs font-medium shadow-xs">
                <x-ui.icon name="lock" class="w-3.5 h-3.5 shrink-0" />
                <span>{{ __('app.privacy.blurred_badge') }}</span>
            </span>
        </div>

        @if ($canReveal && $allowToggle)
            <button
                type="button"
                @click="revealed = !revealed"
                class="absolute top-2.5 right-2.5 z-10 inline-flex items-center gap-1.5 rounded-lg bg-surface/95 border border-border-strong px-2.5 min-h-9 text-xs font-medium text-ink-primary hover:bg-surface cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
            >
                <x-ui.icon name="eye" class="w-3.5 h-3.5" x-show="!revealed" />
                <x-ui.icon name="eye-off" class="w-3.5 h-3.5" x-show="revealed" x-cloak />
                <span x-text="revealed ? @js(__('app.privacy.hide_photo')) : @js(__('app.privacy.reveal_photo'))"></span>
            </button>
        @endif
    @endif
</div>
