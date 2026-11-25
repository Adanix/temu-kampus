@extends('layouts.app')

@section('title', __('app.notifications.title'))
@section('header_title', __('app.notifications.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-0 space-y-1">
            <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
                {{ __('app.notifications.title') }}
            </h1>
            <p class="text-sm text-ink-secondary">{{ __('app.notifications.subtitle') }}</p>
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <x-ui.button type="submit" variant="secondary" size="sm">
                    {{ __('app.actions.mark_all_read') }}
                </x-ui.button>
            </form>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <x-ui.empty-state
            :title="__('app.notifications.empty_title')"
            :description="__('app.notifications.empty_desc')"
            icon="bell"
        />
    @else
        <div class="max-w-4xl space-y-3">
            @foreach ($notifications as $log)
                <article class="bg-surface border {{ $log->isUnread() ? 'border-brand/50' : 'border-border-subtle' }} rounded-xl p-4 sm:p-5 space-y-2.5 min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.badge :tone="$log->event_type->badgeTone()">{{ $log->event_type->label() }}</x-ui.badge>
                            <x-ui.badge tone="neutral">{{ __('app.demo.simulated_delivery') }}</x-ui.badge>
                            @if ($log->isUnread())
                                <x-ui.badge tone="brand">{{ __('app.notifications.unread_badge') }}</x-ui.badge>
                            @endif
                        </div>
                        <span class="font-mono text-xs text-ink-muted tabular-nums">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    <h2 class="text-sm sm:text-base font-semibold text-ink-primary break-words">{{ $log->localizedSubject() }}</h2>
                    <p class="text-sm text-ink-secondary leading-relaxed break-words">{{ $log->localizedBody() }}</p>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <div>
                            @if ($log->relatedItem)
                                <a href="{{ route('items.show', $log->relatedItem) }}" class="text-xs font-medium text-brand hover:underline">
                                    {{ $log->relatedItem->ticket_code }} — {{ __('app.common.view_detail') }}
                                </a>
                            @endif
                        </div>

                        @if ($log->isUnread())
                            <form method="POST" action="{{ route('notifications.read', $log) }}">
                                @csrf
                                <x-ui.button type="submit" variant="ghost" size="sm">
                                    {{ __('app.actions.mark_read') }}
                                </x-ui.button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div>
            {{ $notifications->links() }}
        </div>
    @endif
@endsection
