@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $variants = [
        'primary' => 'bg-brand text-white hover:bg-brand-hover border border-transparent',
        'secondary' => 'bg-surface text-ink-primary border border-border-strong hover:bg-surface-subtle',
        'ghost' => 'bg-transparent text-ink-secondary border border-transparent hover:bg-surface-subtle hover:text-ink-primary',
        'danger' => 'bg-danger text-white hover:bg-danger-hover border border-transparent',
        'signal' => 'bg-signal text-white hover:opacity-95 border border-transparent',
    ];

    $sizes = [
        'sm' => 'min-h-10 px-3 text-xs',
        'md' => 'min-h-11 px-4 text-sm',
        'lg' => 'min-h-12 px-5 text-sm sm:text-base',
    ];

    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-2 rounded-lg font-medium cursor-pointer transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed motion-reduce:transition-none text-center',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
