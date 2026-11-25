@props([
    'name',
    'title',
])

<div
    x-data="{ open: false }"
    @open-modal.window="if ($event.detail === '{{ $name }}') { open = true; }"
    @close-modal.window="if (!$event.detail || $event.detail === '{{ $name }}') { open = false; }"
    @keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-ink-primary/40 backdrop-blur-xs"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title-{{ $name }}"
>
    <div
        @click.outside="open = false"
        x-transition.opacity.duration.150ms
        class="bg-surface border border-border-subtle rounded-2xl shadow-lg w-[min(calc(100vw-1.5rem),34rem)] max-h-[calc(100dvh-2rem)] flex flex-col overflow-hidden"
    >
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-border-subtle shrink-0">
            <h3 id="modal-title-{{ $name }}" class="text-base font-semibold text-ink-primary min-w-0 break-words">
                {{ $title }}
            </h3>
            <button
                type="button"
                @click="open = false"
                aria-label="{{ __('app.common.close') }}"
                class="inline-flex items-center justify-center rounded-lg min-h-11 min-w-11 text-ink-muted hover:text-ink-primary hover:bg-surface-subtle cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
            >
                <x-ui.icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <div class="overflow-y-auto min-h-0 flex-1 p-5">
            {{ $slot }}
        </div>
    </div>
</div>
