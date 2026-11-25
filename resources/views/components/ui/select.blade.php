@props([
    'label',
    'name',
    'id' => null,
    'required' => false,
    'hint' => null,
])

@php
    $selectId = $id ?? 'field_'.str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has($name);
    $describedBy = collect([
        $hint ? $selectId.'_hint' : null,
        $hasError ? $selectId.'_error' : null,
    ])->filter()->implode(' ');
@endphp

<div class="min-w-0">
    <label for="{{ $selectId }}" class="block text-sm font-medium text-ink-primary mb-1.5 cursor-pointer">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-hidden="true">*</span>
        @endif
    </label>

    <select
        id="{{ $selectId }}"
        name="{{ $name }}"
        @if ($required)
            required
        @endif
        @if ($hasError)
            aria-invalid="true"
        @endif
        @if ($describedBy !== '')
            aria-describedby="{{ $describedBy }}"
        @endif
        {{ $attributes->merge([
            'class' => 'w-full min-h-11 rounded-lg border bg-surface px-3 py-2 text-sm text-ink-primary cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:border-brand transition-colors duration-150 '.($hasError ? 'border-danger' : 'border-border-strong'),
        ]) }}
    >
        {{ $slot }}
    </select>

    @if ($hint)
        <p id="{{ $selectId }}_hint" class="mt-1.5 text-xs text-ink-muted break-words">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $selectId }}_error" role="alert" class="mt-1.5 text-xs font-medium text-danger break-words">{{ $message }}</p>
    @enderror
</div>
