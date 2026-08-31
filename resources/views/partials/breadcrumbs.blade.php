@php
    $eventShow = request()->routeIs('events.show') ? request()->route('event') : null;
    $galleryShow = request()->routeIs('galleries.show') ? request()->route('gallery') : null;

    $current = match (true) {
        request()->routeIs('door-opener') => 'Door opener',
        request()->routeIs('wayfinder') => 'Wayfinder',
        request()->routeIs('calendar') => __('Calendar'),
        request()->routeIs('events.index') => __('Events'),
        request()->routeIs('events.show') => $eventShow?->name ?? __('Events'),
        request()->routeIs('galleries.index') => __('Galleries'),
        request()->routeIs('galleries.show') => $galleryShow?->name ?? __('Galleries'),
        request()->routeIs('about') => __('About'),
        request()->routeIs('login') => __('Log in'),
        default => null,
    };
@endphp

@if ($current)
    <nav aria-label="Breadcrumb" class="min-w-0">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-[#706f6c] dark:text-[#A1A09A]">
            <li class="flex items-center gap-1.5">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-1 rounded-sm border border-transparent px-1 py-0.5 transition hover:border-[#19140035] hover:text-[#1b1b18] dark:hover:border-[#3E3E3A] dark:hover:text-[#EDEDEC]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>{{ __('Home') }}</span>
                </a>
            </li>
            @if (request()->routeIs('events.show'))
                <li class="text-[#c4c4c0] dark:text-[#5a5955]" aria-hidden="true">/</li>
                <li>
                    <a
                        href="{{ route('events.index') }}"
                        class="rounded-sm border border-transparent px-1 py-0.5 transition hover:border-[#19140035] hover:text-[#1b1b18] dark:hover:border-[#3E3E3A] dark:hover:text-[#EDEDEC]"
                    >
                        {{ __('Events') }}
                    </a>
                </li>
            @endif
            @if (request()->routeIs('galleries.show'))
                <li class="text-[#c4c4c0] dark:text-[#5a5955]" aria-hidden="true">/</li>
                <li>
                    <a
                        href="{{ route('galleries.index') }}"
                        class="rounded-sm border border-transparent px-1 py-0.5 transition hover:border-[#19140035] hover:text-[#1b1b18] dark:hover:border-[#3E3E3A] dark:hover:text-[#EDEDEC]"
                    >
                        {{ __('Galleries') }}
                    </a>
                </li>
            @endif
            <li class="text-[#c4c4c0] dark:text-[#5a5955]" aria-hidden="true">/</li>
            <li class="min-w-0 truncate font-medium text-[#1b1b18] dark:text-[#EDEDEC]" aria-current="page">{{ $current }}</li>
        </ol>
    </nav>
@endif
