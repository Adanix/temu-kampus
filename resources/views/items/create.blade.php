@extends('layouts.app')

@section('title', __('app.items.create_title'))
@section('header_title', __('app.items.create_title'))

@section('content')
    <div class="max-w-3xl space-y-6">
        <div class="space-y-1">
            <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-secondary hover:text-brand">
                <x-ui.icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>{{ __('app.nav.catalog') }}</span>
            </a>
            <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
                {{ __('app.items.create_title') }}
            </h1>
            <p class="text-sm text-ink-secondary">{{ __('app.items.create_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="bg-surface border border-border-subtle rounded-2xl p-5 sm:p-6 space-y-6">
            @csrf

            @include('items.partials.form-fields')

            <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-border-subtle">
                <x-ui.button :href="route('items.index')" variant="secondary">
                    {{ __('app.common.cancel') }}
                </x-ui.button>
                <x-ui.button type="submit" variant="primary">
                    {{ __('app.actions.save_report') }}
                </x-ui.button>
            </div>
        </form>
    </div>
@endsection
