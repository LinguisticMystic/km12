@extends('layouts.km12')

@section('title', 'About — KM12')

@section('content')
    @php
        $socialLinks = collect(config('community.social'))
            ->filter(fn (array $link) => filled($link['url'] ?? null))
            ->values();
    @endphp

    <div class="w-full max-w-2xl self-stretch">
        <div class="text-center sm:text-left">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                About
            </h1>
            <p class="mt-4 text-base leading-relaxed text-[#706f6c] dark:text-[#A1A09A]">
                {{ config('community.about') }}
            </p>
        </div>

        @if ($socialLinks->isNotEmpty())
            <section class="mt-12" aria-labelledby="connect-heading">
                <h2 id="connect-heading" class="text-center text-lg font-medium sm:text-left">
                    Connect with us
                </h2>
                <p class="mt-2 text-center text-sm text-[#706f6c] sm:text-left dark:text-[#A1A09A]">
                    Find KM12 online or get in touch.
                </p>

                <ul class="mt-6 divide-y divide-[#e3e3e0] overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:divide-[#3E3E3A] dark:border-[#3E3E3A] dark:bg-[#161615]">
                    @foreach ($socialLinks as $link)
                        <li>
                            <a
                                href="{{ $link['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-center gap-4 px-5 py-4 transition hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a]"
                            >
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                                    @include('partials.social-icon', ['icon' => $link['icon'] ?? 'link'])
                                </span>
                                <span class="min-w-0 flex-1 font-medium">{{ $link['label'] }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] transition group-hover:translate-x-0.5 dark:text-[#A1A09A]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
@endsection
