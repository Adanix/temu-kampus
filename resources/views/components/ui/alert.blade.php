@props([
    'tone' => 'success',
    'title' => null,
    'dismissible' => true,
])

@php
    $styles = [
        'success' => 'bg-status-found-bg border-status-found-border text-status-found-text',
        'error' => 'bg-status-danger-bg border-status-danger-border text-status-danger-text',
        'warning' => 'bg-status-pending-bg border-status-pending-border text-status-pending-text',
        'info' => 'bg-status-approved-bg border-status-approved-border text-status-approved-text',
    ];
    $icons = [
        'success' => 'check-circle',
        'error' => 'x-circle',
        'warning' => 'alert-triangle',
        'info' => 'shield-check',
    ];
    $isAssertive = in_array($tone, ['error', 'warning'], true);
@endphp

<div
    x-data="{ shown: true }"
    x-show="shown"
    role="{{ $isAssertive ? 'alert' : 'status' }}"
    aria-live="{{ $isAssertive ? 'assertive' : 'polite' }}"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm min-w-0 '.($styles[$tone] ?? $styles['success'])]) }}
>
    <x-ui.icon :name="$icons[$tone] ?? 'check-circle'" class="w-4 h-4 mt-0.5 shrink-0" />
    <div class="flex-1 min-w-0 break-words">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>
    @if ($dismissible)
        <button
            type="button"
            @click="shown = false"
            aria-label="{{ __('app.common.close') }}"
            class="-my-1 -mr-1 inline-flex items-center justify-center rounded-lg min-h-9 min-w-9 cursor-pointer opacity-80 hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
        >
            <x-ui.icon name="x" class="w-4 h-4" />
        </button>
    @endif
</div>
