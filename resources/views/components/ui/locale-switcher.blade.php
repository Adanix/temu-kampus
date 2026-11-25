@props([
    'compact' => false,
])

@php
    $currentLocale = app()->getLocale();
@endphp

<div class="inline-flex items-center rounded-lg border border-border-subtle bg-surface-subtle p-0.5 shrink-0" role="group" aria-label="Language switcher">
    <form method="POST" action="{{ route('locale.switch') }}">
        @csrf
        <input type="hidden" name="locale" value="id">
        <button
            type="submit"
            aria-pressed="{{ $currentLocale === 'id' ? 'true' : 'false' }}"
            class="px-2.5 {{ $compact ? 'min-h-9' : 'min-h-10' }} rounded-md text-xs font-mono uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand {{ $currentLocale === 'id' ? 'bg-surface text-ink-primary font-semibold shadow-2xs' : 'text-ink-muted hover:text-ink-primary' }}"
        >
            ID
        </button>
    </form>

    <form method="POST" action="{{ route('locale.switch') }}">
        @csrf
        <input type="hidden" name="locale" value="en">
        <button
            type="submit"
            aria-pressed="{{ $currentLocale === 'en' ? 'true' : 'false' }}"
            class="px-2.5 {{ $compact ? 'min-h-9' : 'min-h-10' }} rounded-md text-xs font-mono uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand {{ $currentLocale === 'en' ? 'bg-surface text-ink-primary font-semibold shadow-2xs' : 'text-ink-muted hover:text-ink-primary' }}"
        >
            EN
        </button>
    </form>
</div>
