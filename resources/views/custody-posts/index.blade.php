@extends('layouts.app')

@section('title', __('app.custody_posts.title'))
@section('header_title', __('app.custody_posts.title'))

@section('content')
    <div class="space-y-1">
        <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
            {{ __('app.custody_posts.title') }}
        </h1>
        <p class="text-sm text-ink-secondary">{{ __('app.custody_posts.subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($custodyPosts as $post)
            <article class="bg-surface border border-border-subtle rounded-xl p-5 flex flex-col justify-between gap-4 min-w-0">
                <div class="space-y-2.5 min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <x-ui.badge tone="brand" mono>{{ $post->campusZone->code }}</x-ui.badge>
                        <x-ui.badge tone="neutral" mono>{{ __('app.custody_posts.active_items', ['count' => $post->open_items_count]) }}</x-ui.badge>
                    </div>
                    <h2 class="text-base font-semibold text-ink-primary break-words">{{ $post->localizedName() }}</h2>
                    <p class="text-xs text-ink-secondary">{{ $post->campusZone->localizedName() }}</p>
                    <div class="pt-1 space-y-1 text-xs text-ink-muted">
                        <p class="inline-flex items-center gap-1.5">
                            <x-ui.icon name="clock" class="w-3.5 h-3.5 shrink-0" />
                            <span>{{ $post->operating_hours }}</span>
                        </p>
                        @if ($post->contact_ext)
                            <p class="font-mono">{{ $post->contact_ext }}</p>
                        @endif
                    </div>
                </div>

                <x-ui.button :href="route('items.index', ['type' => 'found', 'zone_id' => $post->campus_zone_id])" variant="secondary" size="sm" class="w-full">
                    {{ __('app.custody_posts.view_items_in_zone') }}
                </x-ui.button>
            </article>
        @endforeach
    </div>
@endsection
