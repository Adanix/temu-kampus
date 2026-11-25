@extends('layouts.app')

@section('title', __('app.items.edit_title', ['code' => $item->ticket_code]))
@section('header_title', __('app.items.edit_title', ['code' => $item->ticket_code]))

@section('content')
    <div class="max-w-3xl space-y-6">
        <div class="space-y-1">
            <a href="{{ route('items.show', $item) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-secondary hover:text-brand">
                <x-ui.icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>{{ $item->ticket_code }}</span>
            </a>
            <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
                {{ __('app.items.edit_title', ['code' => $item->ticket_code]) }}
            </h1>
            <p class="text-sm text-ink-secondary">{{ __('app.items.edit_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data" class="bg-surface border border-border-subtle rounded-2xl p-5 sm:p-6 space-y-6">
            @csrf
            @method('PUT')

            @include('items.partials.form-fields', ['item' => $item])

            <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-border-subtle">
                <x-ui.button :href="route('items.show', $item)" variant="secondary">
                    {{ __('app.common.cancel') }}
                </x-ui.button>
                <x-ui.button type="submit" variant="primary">
                    {{ __('app.actions.update_report') }}
                </x-ui.button>
            </div>
        </form>
    </div>
@endsection
