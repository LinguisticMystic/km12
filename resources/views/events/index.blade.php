@extends('layouts.km12')

@section('title', __('Events').' — KM12')

@section('content')
    <div class="w-full max-w-2xl self-stretch">
        <div class="text-center sm:text-left">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                {{ __('Events') }}
            </h1>
            <p class="mt-4 text-base leading-relaxed text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Upcoming and past events at KM12.') }}
            </p>
        </div>

        @if ($events->isEmpty())
            <p class="mt-12 text-center text-sm text-[#706f6c] sm:text-left dark:text-[#A1A09A]">
                {{ __('No events yet. Check back soon.') }}
            </p>
        @else
            <ul class="mt-12 divide-y divide-[#e3e3e0] overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:divide-[#3E3E3A] dark:border-[#3E3E3A] dark:bg-[#161615]">
                @foreach ($events as $event)
                    <li>
                        <a
                            href="{{ route('events.show', $event) }}"
                            class="group flex items-center gap-4 px-5 py-4 transition hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a]"
                        >
                            @if ($event->posterUrl())
                                <img
                                    src="{{ $event->posterUrl() }}"
                                    alt=""
                                    class="size-14 shrink-0 rounded-xl object-cover"
                                >
                            @else
                                <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                </span>
                            @endif
                            <span class="min-w-0 flex-1">
                                <span class="block font-medium">{{ $event->name }}</span>
                                <span class="mt-0.5 block text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ localized_date($event->date->timezone(config('app.timezone')), 'D, j M Y · H:i') }}
                                </span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] transition group-hover:translate-x-0.5 dark:text-[#A1A09A]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
