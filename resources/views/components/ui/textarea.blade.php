@props([
    'label',
    'name',
    'id' => null,
    'rows' => 4,
    'required' => false,
    'hint' => null,
    'value' => null,
    'placeholder' => '',
])

@php
    $textareaId = $id ?? 'field_'.str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has($name);
    $describedBy = collect([
        $hint ? $textareaId.'_hint' : null,
        $hasError ? $textareaId.'_error' : null,
    ])->filter()->implode(' ');
@endphp

<div class="min-w-0">
    <label for="{{ $textareaId }}" class="block text-sm font-medium text-ink-primary mb-1.5 cursor-pointer">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-hidden="true">*</span>
        @endif
    </label>

    <textarea
        id="{{ $textareaId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
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
            'class' => 'w-full rounded-lg border bg-surface px-3.5 py-2.5 text-sm text-ink-primary placeholder:text-ink-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:border-brand transition-colors duration-150 '.($hasError ? 'border-danger' : 'border-border-strong'),
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @if ($hint)
        <p id="{{ $textareaId }}_hint" class="mt-1.5 text-xs text-ink-muted break-words">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $textareaId }}_error" role="alert" class="mt-1.5 text-xs font-medium text-danger break-words">{{ $message }}</p>
    @enderror
</div>
