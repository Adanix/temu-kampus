@extends('layouts.auth-demo')

@section('title', __('app.demo.login_title').' — '.__('app.brand'))

@section('content')
    <div class="space-y-6">
        <div class="space-y-2 text-center max-w-2xl mx-auto">
            <div class="inline-flex items-center gap-2 text-brand font-bold text-lg justify-center">
                <span class="w-7 h-7 rounded-md bg-brand text-white inline-flex items-center justify-center shrink-0">
                    <x-ui.icon name="shield-check" class="w-4 h-4" />
                </span>
                <span>{{ __('app.brand') }}</span>
            </div>
            <h1 class="text-[clamp(1.375rem,1.6vw+0.85rem,1.875rem)] leading-[1.2] tracking-[-0.02em] font-semibold text-ink-primary text-balance">
                {{ __('app.demo.login_title') }}
            </h1>
            <p class="text-sm text-ink-secondary leading-relaxed">
                {{ __('app.demo.login_subtitle') }}
            </p>
        </div>

        @if ($currentUser)
            <div class="bg-surface border border-border-subtle rounded-xl p-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-ink-primary min-w-0 break-words">
                    {{ __('app.demo.already_signed_in', ['name' => $currentUser->name, 'role' => $currentUser->role->label()]) }}
                </p>
                <x-ui.button :href="route($currentUser->defaultLandingRoute())" variant="secondary" size="sm">
                    {{ __('app.common.return_to_dashboard') }}
                </x-ui.button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($personas as $personaKey => $persona)
                @php
                    $roleEnum = \App\Enums\UserRole::from($persona['role']);
                    $isActive = $currentUser && $currentUser->email === $persona['email'];
                @endphp
                <div class="bg-surface border {{ $isActive ? 'border-brand ring-1 ring-brand' : 'border-border-subtle' }} rounded-xl p-5 flex flex-col justify-between gap-4 min-w-0">
                    <div class="space-y-3 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-11 h-11 rounded-lg bg-brand-tint text-brand font-semibold text-sm inline-flex items-center justify-center shrink-0">
                                    {{ $persona['initials'] }}
                                </span>
                                <div class="min-w-0">
                                    <h2 class="text-base font-semibold text-ink-primary truncate">{{ $persona['name'] }}</h2>
                                    <p class="text-xs text-ink-muted truncate">{{ $persona['campus_unit'] }}</p>
                                </div>
                            </div>
                            <x-ui.badge :tone="$roleEnum->badgeTone()">
                                {{ $roleEnum->label() }}
                            </x-ui.badge>
                        </div>

                        <p class="text-sm text-ink-secondary leading-relaxed break-words">
                            {{ __('app.demo.persona_summaries.'.$personaKey) }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('demo.quick-login') }}">
                        @csrf
                        <input type="hidden" name="persona" value="{{ $personaKey }}">
                        <x-ui.button type="submit" :variant="$personaKey === 'student' ? 'primary' : 'secondary'" class="w-full">
                            {{ __('app.demo.persona_cta.'.$personaKey) }}
                        </x-ui.button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endsection
