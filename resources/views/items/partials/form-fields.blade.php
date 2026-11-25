@php
    $isEdit = isset($item);
    $initialType = $isEdit ? $item->type->value : old('type', $defaultType ?? 'lost');
    $initialCategory = old('category', $isEdit ? $item->category->value : '');
    $initialCustodyType = old('custody_type', $isEdit ? ($item->custody_type?->value ?? 'at_custody_post') : 'at_custody_post');
@endphp

<div
    x-data="{
        type: @js($initialType),
        category: @js($initialCategory),
        custodyType: @js($initialCustodyType),
        submitting: false
    }"
    class="space-y-5"
>
    @if (! $isEdit)
        <div>
            <span class="block text-sm font-medium text-ink-primary mb-2">{{ __('app.items.fields.type') }}</span>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="lost" x-model="type" class="sr-only">
                    <span
                        :class="type === 'lost' ? 'border-signal bg-status-lost-bg text-status-lost-text font-semibold' : 'border-border-strong bg-surface text-ink-secondary'"
                        class="flex items-center justify-center gap-2 rounded-lg border min-h-11 px-3 text-sm transition-colors duration-150"
                    >
                        {{ __('enums.item_type.lost') }}
                    </span>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="found" x-model="type" class="sr-only">
                    <span
                        :class="type === 'found' ? 'border-brand bg-brand-tint text-brand font-semibold' : 'border-border-strong bg-surface text-ink-secondary'"
                        class="flex items-center justify-center gap-2 rounded-lg border min-h-11 px-3 text-sm transition-colors duration-150"
                    >
                        {{ __('enums.item_type.found') }}
                    </span>
                </label>
            </div>
        </div>
    @else
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-ink-secondary">{{ __('app.items.fields.type') }}:</span>
            <x-ui.badge :tone="$item->type->badgeTone()">{{ $item->type->label() }}</x-ui.badge>
        </div>
    @endif

    <x-ui.input
        name="title"
        :label="__('app.items.fields.title')"
        :placeholder="__('app.items.fields.title_placeholder')"
        :value="$isEdit ? $item->title : null"
        required
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-ui.select
            name="category"
            :label="__('app.items.fields.category')"
            x-model="category"
            required
        >
            <option value="">{{ __('app.items.fields.select_category') }}</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->value }}" @selected(old('category', $isEdit ? $item->category->value : '') === $cat->value)>
                    {{ $cat->label() }}
                </option>
            @endforeach
        </x-ui.select>

        <x-ui.input
            name="incident_date"
            type="date"
            :label="__('app.items.fields.incident_date')"
            :value="$isEdit ? $item->incident_date->toDateString() : now()->toDateString()"
            max="{{ now()->toDateString() }}"
            required
        />
    </div>

    <div x-show="category === 'id_document'" x-cloak>
        <x-ui.alert tone="warning" :dismissible="false">
            {{ __('app.privacy.id_warning') }}
        </x-ui.alert>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-ui.select
            name="campus_zone_id"
            :label="__('app.items.fields.campus_zone_id')"
            required
        >
            <option value="">{{ __('app.items.fields.select_zone') }}</option>
            @foreach ($zones as $zone)
                <option value="{{ $zone->id }}" @selected((string) old('campus_zone_id', $isEdit ? $item->campus_zone_id : '') === (string) $zone->id)>
                    {{ $zone->code }} — {{ $zone->localizedName() }}
                </option>
            @endforeach
        </x-ui.select>

        <x-ui.input
            name="location_detail"
            :label="__('app.items.fields.location_detail')"
            :placeholder="__('app.items.fields.location_detail_placeholder')"
            :value="$isEdit ? $item->location_detail : null"
        />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-ui.input
            name="brand"
            :label="__('app.items.fields.brand')"
            :value="$isEdit ? $item->brand : null"
        />
        <x-ui.input
            name="color"
            :label="__('app.items.fields.color')"
            :value="$isEdit ? $item->color : null"
        />
    </div>

    <x-ui.textarea
        name="description"
        :label="__('app.items.fields.description')"
        :hint="__('app.items.fields.description_hint')"
        :value="$isEdit ? $item->description : null"
        rows="4"
        required
    />

    @if ($isEdit && $item->hasUploadedOrSeededPhoto())
        <div class="space-y-1.5">
            <p class="text-xs font-medium text-ink-secondary">{{ __('app.items.fields.current_photo') }}</p>
            <div class="w-40 rounded-xl overflow-hidden border border-border-subtle">
                <x-ui.item-thumbnail :item="$item" :canReveal="false" :allowToggle="false" />
            </div>
        </div>
    @endif

    <x-ui.input
        name="photo"
        type="file"
        accept=".jpg,.jpeg,.png,.webp"
        :label="__('app.items.fields.photo')"
    />

    <div x-show="type === 'found'" x-cloak class="rounded-xl border border-status-found-border bg-brand-tint/40 p-4 sm:p-5 space-y-4">
        <div class="flex items-center gap-2 text-brand">
            <x-ui.icon name="lock" class="w-4 h-4 shrink-0" />
            <h3 class="text-sm font-semibold">{{ __('app.privacy.hidden_traits_title') }}</h3>
        </div>

        <x-ui.textarea
            name="hidden_traits"
            :label="__('app.items.fields.hidden_traits')"
            :hint="__('app.privacy.hidden_traits_hint')"
            :value="$isEdit ? $item->hidden_traits : null"
            rows="3"
        />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-ui.select
                name="custody_type"
                :label="__('app.items.fields.custody_type')"
                x-model="custodyType"
            >
                @foreach (\App\Enums\CustodyType::cases() as $ct)
                    <option value="{{ $ct->value }}" @selected(old('custody_type', $isEdit ? $item->custody_type?->value : 'at_custody_post') === $ct->value)>
                        {{ $ct->label() }}
                    </option>
                @endforeach
            </x-ui.select>

            <div x-show="custodyType === 'at_custody_post'" x-cloak>
                <x-ui.select
                    name="custody_post_id"
                    :label="__('app.items.fields.custody_post_id')"
                >
                    <option value="">{{ __('app.items.fields.select_post') }}</option>
                    @foreach ($custodyPosts as $post)
                        <option value="{{ $post->id }}" @selected((string) old('custody_post_id', $isEdit ? $item->custody_post_id : '') === (string) $post->id)>
                            {{ $post->localizedName() }} ({{ $post->operating_hours }})
                        </option>
                    @endforeach
                </x-ui.select>
            </div>
        </div>
    </div>
</div>
