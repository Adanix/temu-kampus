@props([
    'label',
    'name',
    'id' => null,
    'type' => 'text',
    'required' => false,
    'hint' => null,
    'value' => null,
    'placeholder' => '',
])

@php
    $inputId = $id ?? 'field_'.str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has($name);
    $describedBy = collect([
        $hint ? $inputId.'_hint' : null,
        $hasError ? $inputId.'_error' : null,
    ])->filter()->implode(' ');
@endphp

<div class="min-w-0">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-ink-primary mb-1.5 cursor-pointer">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-hidden="true">*</span>
        @endif
    </label>

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'file')
            value="{{ old($name, $value) }}"
        @endif
        @if ($placeholder !== '')
            placeholder="{{ $placeholder }}"
        @endif
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
            'class' => 'w-full min-h-11 rounded-lg border bg-surface px-3.5 py-2 text-sm text-ink-primary placeholder:text-ink-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:border-brand transition-colors duration-150 '.($hasError ? 'border-danger' : 'border-border-strong'),
        ]) }}
    >

    @if ($hint)
        <p id="{{ $inputId }}_hint" class="mt-1.5 text-xs text-ink-muted break-words">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $inputId }}_error" role="alert" class="mt-1.5 text-xs font-medium text-danger break-words">{{ $message }}</p>
    @enderror
</div>
