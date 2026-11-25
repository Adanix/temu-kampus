@php
    $user = auth()->user();
    $navItems = [
        ['route' => 'dashboard', 'pattern' => 'dashboard', 'label' => __('app.nav.dashboard'), 'icon' => 'layout-dashboard', 'show' => true],
        ['route' => 'items.index', 'pattern' => 'items.index', 'label' => __('app.nav.catalog'), 'icon' => 'search', 'show' => true],
        ['route' => 'items.create', 'pattern' => 'items.create', 'label' => __('app.nav.report_item'), 'icon' => 'plus', 'show' => true],
        ['route' => 'matches.index', 'pattern' => 'matches.*', 'label' => __('app.nav.matches'), 'icon' => 'sparkles', 'show' => true],
        ['route' => 'claims.index', 'pattern' => 'claims.index', 'label' => __('app.nav.my_claims'), 'icon' => 'clipboard-check', 'show' => ! $user->isCustodian()],
        ['route' => 'custody-posts.index', 'pattern' => 'custody-posts.*', 'label' => __('app.nav.custody_posts'), 'icon' => 'building', 'show' => true],
        ['route' => 'custodian.desk', 'pattern' => 'custodian.*', 'label' => __('app.nav.custodian_desk'), 'icon' => 'shield-check', 'show' => $user->can('access-custodian-desk'), 'badge' => $pendingClaimsCount ?? 0],
        ['route' => 'admin.analytics', 'pattern' => 'admin.analytics', 'label' => __('app.nav.admin_analytics'), 'icon' => 'bar-chart', 'show' => $user->can('access-admin-analytics')],
        ['route' => 'admin.master.index', 'pattern' => 'admin.master.*', 'label' => __('app.nav.admin_master'), 'icon' => 'settings', 'show' => $user->can('manage-campus-master')],
    ];
@endphp

<div class="p-4 border-b border-border-subtle flex items-center justify-between gap-2">
    <a href="{{ route($user->defaultLandingRoute()) }}" class="inline-flex items-center gap-2.5 text-ink-primary font-bold tracking-tight text-base min-w-0">
        <span class="w-7 h-7 rounded-md bg-brand text-white inline-flex items-center justify-center shrink-0">
            <x-ui.icon name="shield-check" class="w-4 h-4" />
        </span>
        <span class="truncate">{{ __('app.brand') }}</span>
    </a>
    @if ($isMobile ?? false)
        <button
            type="button"
            @click="mobileNavOpen = false"
            aria-label="{{ __('app.common.close_menu') }}"
            class="inline-flex items-center justify-center rounded-lg min-h-11 min-w-11 text-ink-secondary hover:bg-surface-subtle cursor-pointer"
        >
            <x-ui.icon name="x" class="w-5 h-5" />
        </button>
    @endif
</div>

<nav class="flex-1 p-3 space-y-1 overflow-y-auto" aria-label="Workspace navigation">
    @foreach ($navItems as $item)
        @if ($item['show'])
            @php $isActive = request()->routeIs($item['pattern']); @endphp
            <a
                href="{{ route($item['route']) }}"
                @if ($isActive) aria-current="page" @endif
                class="flex items-center justify-between gap-2 rounded-lg px-3 min-h-11 text-sm transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand {{ $isActive ? 'bg-brand-tint text-brand font-semibold' : 'text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary' }}"
            >
                <span class="inline-flex items-center gap-2.5 min-w-0">
                    <x-ui.icon :name="$item['icon']" class="w-4 h-4 shrink-0" />
                    <span class="truncate">{{ $item['label'] }}</span>
                </span>
                @if (! empty($item['badge']) && $item['badge'] > 0)
                    <span class="inline-flex items-center justify-center rounded-md bg-brand text-white font-mono text-[11px] font-semibold px-1.5 py-0.5 tabular-nums">
                        {{ $item['badge'] }}
                    </span>
                @endif
            </a>
        @endif
    @endforeach
</nav>

<div class="p-3 border-t border-border-subtle space-y-1">
    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between gap-2 rounded-lg px-3 min-h-11 text-sm transition-colors duration-150 {{ request()->routeIs('notifications.*') ? 'bg-brand-tint text-brand font-semibold' : 'text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary' }}">
        <span class="inline-flex items-center gap-2.5 min-w-0">
            <x-ui.icon name="bell" class="w-4 h-4 shrink-0" />
            <span class="truncate">{{ __('app.nav.notifications') }}</span>
        </span>
        @if (($unreadNotificationCount ?? 0) > 0)
            <span class="inline-flex items-center justify-center rounded-md bg-signal text-white font-mono text-[11px] font-semibold px-1.5 py-0.5 tabular-nums">
                {{ $unreadNotificationCount }}
            </span>
        @endif
    </a>
    <a href="{{ route('public.docs') }}" class="flex items-center gap-2.5 rounded-lg px-3 min-h-11 text-sm text-ink-secondary hover:bg-surface-subtle hover:text-ink-primary transition-colors duration-150">
        <x-ui.icon name="file-text" class="w-4 h-4 shrink-0" />
        <span class="truncate">{{ __('app.nav.docs') }}</span>
    </a>
</div>
