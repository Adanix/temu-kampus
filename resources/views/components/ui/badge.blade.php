@props([
    'tone' => 'neutral',
    'mono' => false,
])

@php
    $tones = [
        'lost' => 'bg-status-lost-bg border-status-lost-border text-status-lost-text',
        'found' => 'bg-status-found-bg border-status-found-border text-status-found-text',
        'pending' => 'bg-status-pending-bg border-status-pending-border text-status-pending-text',
        'approved' => 'bg-status-approved-bg border-status-approved-border text-status-approved-text',
        'danger' => 'bg-status-danger-bg border-status-danger-border text-status-danger-text',
        'neutral' => 'bg-status-neutral-bg border-status-neutral-border text-status-neutral-text',
        'brand' => 'bg-brand-tint border-status-found-border text-brand',
    ];

    $classes = implode(' ', [
        'inline-flex items-center gap-1.5 rounded-md border px-2 py-0.5 text-xs font-medium whitespace-nowrap',
        $tones[$tone] ?? $tones['neutral'],
        $mono ? 'font-mono tabular-nums' : '',
    ]);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
