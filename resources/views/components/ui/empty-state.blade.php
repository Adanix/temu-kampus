@props([
    'title',
    'description' => null,
    'icon' => 'inbox',
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'bg-surface border border-border-subtle rounded-xl p-6 sm:p-8 text-center flex flex-col items-center gap-3 min-w-0']) }}>
    <div class="w-11 h-11 rounded-xl bg-surface-subtle border border-border-subtle flex items-center justify-center text-ink-muted shrink-0">
        <x-ui.icon :name="$icon" class="w-5 h-5" />
    </div>
    <h3 class="text-base font-semibold text-ink-primary break-words">{{ $title }}</h3>
    @if ($description)
        <p class="text-sm text-ink-secondary max-w-md break-words">{{ $description }}</p>
    @endif
    @if ($actionLabel && $actionHref)
        <div class="pt-1">
            <x-ui.button :href="$actionHref" variant="secondary" size="sm">
                {{ $actionLabel }}
            </x-ui.button>
        </div>
    @endif
</div>
