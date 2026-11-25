@extends('layouts.app')

@section('title', __('app.catalog.title'))
@section('header_title', __('app.catalog.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-0 space-y-1">
            <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] leading-[1.2] tracking-[-0.02em] font-semibold text-ink-primary">
                {{ __('app.catalog.title') }}
            </h1>
            <p class="text-sm text-ink-secondary">{{ __('app.catalog.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('items.create', ['type' => 'lost'])" variant="signal" size="sm">
                <x-ui.icon name="plus" class="w-4 h-4" />
                <span>{{ __('app.actions.report_lost') }}</span>
            </x-ui.button>
            <x-ui.button :href="route('items.create', ['type' => 'found'])" variant="primary" size="sm">
                <x-ui.icon name="plus" class="w-4 h-4" />
                <span>{{ __('app.actions.report_found') }}</span>
            </x-ui.button>
        </div>
    </div>

    <form method="GET" action="{{ route('items.index') }}" class="bg-surface border border-border-subtle rounded-xl p-4 space-y-3" x-data="{ filtersOpen: {{ $activeFilterCount > 0 ? 'true' : 'false' }} }">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1 min-w-0">
                <label for="catalog-search" class="sr-only">{{ __('app.common.search') }}</label>
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-muted pointer-events-none">
                    <x-ui.icon name="search" class="w-4 h-4" />
                </span>
                <input
                    id="catalog-search"
                    name="search"
                    type="search"
                    value="{{ $filters['search'] }}"
                    placeholder="{{ __('app.catalog.search_placeholder') }}"
                    class="w-full min-h-11 rounded-lg border border-border-strong bg-surface pl-10 pr-3.5 text-sm text-ink-primary placeholder:text-ink-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                >
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <x-ui.button type="submit" size="md">{{ __('app.common.search') }}</x-ui.button>
                <button
                    type="button"
                    @click="filtersOpen = !filtersOpen"
                    class="md:hidden inline-flex items-center justify-center gap-1.5 rounded-lg border border-border-strong bg-surface px-3 min-h-11 text-sm font-medium text-ink-primary cursor-pointer"
                >
                    <x-ui.icon name="sliders" class="w-4 h-4" />
                    <span>{{ __('app.common.filters') }}@if ($activeFilterCount > 0) ({{ $activeFilterCount }})@endif</span>
                </button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 pt-1">
            @foreach (['' => __('app.catalog.type_all'), 'lost' => __('enums.item_type.lost'), 'found' => __('enums.item_type.found')] as $typeVal => $typeLabel)
                <button
                    type="submit"
                    name="type"
                    value="{{ $typeVal }}"
                    class="px-3 min-h-9 rounded-lg border text-xs font-medium cursor-pointer transition-colors duration-150 {{ $filters['type'] === $typeVal ? 'bg-brand text-white border-brand' : 'bg-surface-subtle text-ink-secondary border-border-subtle hover:text-ink-primary' }}"
                >
                    {{ $typeLabel }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-border-subtle" :class="filtersOpen ? 'grid' : 'hidden md:grid'">
            <div>
                <label for="filter-category" class="block text-xs font-medium text-ink-muted mb-1">{{ __('app.items.fields.category') }}</label>
                <select id="filter-category" name="category" class="w-full min-h-11 rounded-lg border border-border-strong bg-surface px-3 text-sm text-ink-primary cursor-pointer">
                    <option value="">{{ __('app.catalog.category_all') }}</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->value }}" @selected($filters['category'] === $cat->value)>{{ $cat->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter-zone" class="block text-xs font-medium text-ink-muted mb-1">{{ __('app.items.fields.campus_zone_id') }}</label>
                <select id="filter-zone" name="zone_id" class="w-full min-h-11 rounded-lg border border-border-strong bg-surface px-3 text-sm text-ink-primary cursor-pointer">
                    <option value="">{{ __('app.catalog.zone_all') }}</option>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}" @selected((string) $filters['zone_id'] === (string) $zone->id)>{{ $zone->code }} — {{ $zone->localizedName() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter-status" class="block text-xs font-medium text-ink-muted mb-1">Status</label>
                <select id="filter-status" name="status" class="w-full min-h-11 rounded-lg border border-border-strong bg-surface px-3 text-sm text-ink-primary cursor-pointer">
                    <option value="all" @selected($filters['status'] === 'all')>{{ __('app.catalog.status_all') }}</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" @selected($filters['status'] === $st->value)>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1 min-w-0">
                    <label for="filter-sort" class="block text-xs font-medium text-ink-muted mb-1">Sort</label>
                    <select id="filter-sort" name="sort" class="w-full min-h-11 rounded-lg border border-border-strong bg-surface px-3 text-sm text-ink-primary cursor-pointer">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('app.common.sort_latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('app.common.sort_oldest') }}</option>
                    </select>
                </div>
                <x-ui.button :href="route('items.index')" variant="ghost" size="md">{{ __('app.actions.reset_filters') }}</x-ui.button>
            </div>
        </div>
    </form>

    @if ($items->isEmpty())
        <x-ui.empty-state
            :title="__('app.catalog.empty_title')"
            :description="__('app.catalog.empty_desc')"
            icon="search"
            :actionLabel="__('app.actions.reset_filters')"
            :actionHref="route('items.index')"
        />
    @else
        <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,17.5rem),1fr))] gap-4">
            @foreach ($items as $item)
                <article class="bg-surface border border-border-subtle hover:border-border-strong rounded-xl overflow-hidden flex flex-col min-w-0 transition-colors duration-150">
                    <x-ui.item-thumbnail :item="$item" :canReveal="false" :allowToggle="false" />

                    <div class="p-4 flex-1 flex flex-col justify-between gap-3 min-w-0">
                        <div class="space-y-2 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-1.5">
                                <span class="font-mono text-xs text-ink-muted">{{ $item->ticket_code }}</span>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <x-ui.badge :tone="$item->type->badgeTone()">{{ $item->type->label() }}</x-ui.badge>
                                    <x-ui.badge :tone="$item->status->badgeTone()">{{ $item->status->label() }}</x-ui.badge>
                                </div>
                            </div>

                            <h2 class="text-sm sm:text-base font-semibold text-ink-primary leading-snug min-w-0 break-words">
                                <a href="{{ route('items.show', $item) }}" class="hover:text-brand focus-visible:outline-none focus-visible:underline">
                                    {{ $item->title }}
                                </a>
                            </h2>

                            <p class="text-xs text-ink-secondary line-clamp-2 break-words">{{ $item->description }}</p>
                        </div>

                        <div class="pt-2 border-t border-border-subtle space-y-1.5 text-xs text-ink-muted">
                            <p class="flex items-center gap-1.5 min-w-0">
                                <x-ui.icon name="map-pin" class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate">{{ $item->campusZone->localizedName() }}</span>
                            </p>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 font-mono">
                                    <x-ui.icon name="calendar" class="w-3.5 h-3.5 shrink-0" />
                                    <span>{{ $item->incident_date->format('Y-m-d') }}</span>
                                </span>
                                <a href="{{ route('items.show', $item) }}" class="font-medium text-brand hover:underline">
                                    {{ __('app.common.view_detail') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pt-2">
            {{ $items->links() }}
        </div>
    @endif
@endsection
