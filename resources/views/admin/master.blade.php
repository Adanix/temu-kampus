@extends('layouts.app')

@section('title', __('app.admin.master_title'))
@section('header_title', __('app.admin.master_title'))

@section('content')
    <div class="space-y-1">
        <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] font-semibold text-ink-primary">
            {{ __('app.admin.master_title') }}
        </h1>
        <p class="text-sm text-ink-secondary">{{ __('app.admin.master_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
        <section class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-5 min-w-0">
            <h2 class="text-base font-semibold text-ink-primary">{{ __('app.admin.zones_section') }}</h2>

            <form method="POST" action="{{ route('admin.zones.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-xl bg-surface-subtle/60 border border-border-subtle">
                @csrf
                <x-ui.input name="code" :label="__('app.admin.zone_fields.code')" placeholder="FK-01" required />
                <x-ui.input name="zone_group" :label="__('app.admin.zone_fields.zone_group')" placeholder="Saintek" required />
                <x-ui.input name="name_id" :label="__('app.admin.zone_fields.name_id')" required />
                <x-ui.input name="name_en" :label="__('app.admin.zone_fields.name_en')" required />
                <div class="sm:col-span-2 flex justify-end">
                    <x-ui.button type="submit" size="sm">{{ __('app.actions.add_zone') }}</x-ui.button>
                </div>
            </form>

            <div class="divide-y divide-border-subtle">
                @foreach ($zones as $zone)
                    <div class="py-3 space-y-2" x-data="{ editing: false }">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="min-w-0">
                                <span class="font-mono text-xs font-semibold text-brand">{{ $zone->code }}</span>
                                <span class="text-sm font-medium text-ink-primary ml-1.5">{{ $zone->name_id }}</span>
                                <span class="block text-xs text-ink-muted">{{ $zone->name_en }} · {{ $zone->zone_group }} ({{ __('app.common.items_count', ['count' => $zone->item_reports_count]) }})</span>
                            </div>
                            <x-ui.button type="button" variant="ghost" size="sm" @click="editing = !editing">{{ __('app.common.edit') }}</x-ui.button>
                        </div>

                        <form x-show="editing" x-cloak method="POST" action="{{ route('admin.zones.update', $zone) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2">
                            @csrf
                            @method('PUT')
                            <x-ui.input name="code" :label="__('app.admin.zone_fields.code')" :value="$zone->code" required />
                            <x-ui.input name="zone_group" :label="__('app.admin.zone_fields.zone_group')" :value="$zone->zone_group" required />
                            <x-ui.input name="name_id" :label="__('app.admin.zone_fields.name_id')" :value="$zone->name_id" required />
                            <x-ui.input name="name_en" :label="__('app.admin.zone_fields.name_en')" :value="$zone->name_en" required />
                            <div class="sm:col-span-2 flex justify-end">
                                <x-ui.button type="submit" size="sm">{{ __('app.actions.update_zone') }}</x-ui.button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="bg-surface border border-border-subtle rounded-2xl p-5 space-y-5 min-w-0">
            <h2 class="text-base font-semibold text-ink-primary">{{ __('app.admin.posts_section') }}</h2>

            <form method="POST" action="{{ route('admin.custody-posts.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-xl bg-surface-subtle/60 border border-border-subtle">
                @csrf
                <x-ui.select name="campus_zone_id" :label="__('app.admin.post_fields.campus_zone_id')" required>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->code }} — {{ $zone->localizedName() }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input name="operating_hours" :label="__('app.admin.post_fields.operating_hours')" placeholder="08:00-17:00 WIB" required />
                <x-ui.input name="name_id" :label="__('app.admin.post_fields.name_id')" required />
                <x-ui.input name="name_en" :label="__('app.admin.post_fields.name_en')" required />
                <x-ui.input name="contact_ext" :label="__('app.admin.post_fields.contact_ext')" placeholder="Ext. 105" />
                <div class="flex items-end justify-end">
                    <x-ui.button type="submit" size="sm">{{ __('app.actions.add_post') }}</x-ui.button>
                </div>
            </form>

            <div class="divide-y divide-border-subtle">
                @foreach ($custodyPosts as $post)
                    <div class="py-3 space-y-2" x-data="{ editing: false }">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-ink-primary">{{ $post->name_id }}</span>
                                <span class="block text-xs text-ink-muted">{{ $post->campusZone->code }} · {{ $post->operating_hours }} {{ $post->contact_ext }}</span>
                            </div>
                            <x-ui.button type="button" variant="ghost" size="sm" @click="editing = !editing">{{ __('app.common.edit') }}</x-ui.button>
                        </div>

                        <form x-show="editing" x-cloak method="POST" action="{{ route('admin.custody-posts.update', $post) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2">
                            @csrf
                            @method('PUT')
                            <x-ui.select name="campus_zone_id" :label="__('app.admin.post_fields.campus_zone_id')" required>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}" @selected($post->campus_zone_id === $zone->id)>{{ $zone->code }} — {{ $zone->localizedName() }}</option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.input name="operating_hours" :label="__('app.admin.post_fields.operating_hours')" :value="$post->operating_hours" required />
                            <x-ui.input name="name_id" :label="__('app.admin.post_fields.name_id')" :value="$post->name_id" required />
                            <x-ui.input name="name_en" :label="__('app.admin.post_fields.name_en')" :value="$post->name_en" required />
                            <x-ui.input name="contact_ext" :label="__('app.admin.post_fields.contact_ext')" :value="$post->contact_ext" />
                            <div class="flex items-end justify-end">
                                <x-ui.button type="submit" size="sm">{{ __('app.actions.update_post') }}</x-ui.button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
