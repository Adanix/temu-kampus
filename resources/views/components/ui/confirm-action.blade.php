@props([
    'modalName',
    'title',
    'message',
    'actionUrl',
    'confirmLabel',
    'confirmVariant' => 'danger',
])

<x-ui.modal :name="$modalName" :title="$title">
    <p class="text-sm text-ink-secondary leading-relaxed mb-5 break-words">
        {{ $message }}
    </p>

    <form method="POST" action="{{ $actionUrl }}" class="flex flex-wrap items-center justify-end gap-2">
        @csrf
        <x-ui.button type="button" variant="secondary" @click="$dispatch('close-modal', '{{ $modalName }}')">
            {{ __('app.common.cancel') }}
        </x-ui.button>
        <x-ui.button type="submit" :variant="$confirmVariant">
            {{ $confirmLabel }}
        </x-ui.button>
    </form>
</x-ui.modal>
