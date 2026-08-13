@extends('layouts.km12')

@section('title', $event->name.' — KM12')

@section('content')
    <article class="w-full max-w-4xl self-stretch">
        @if ($event->posterUrl())
            <img
                src="{{ $event->posterUrl() }}"
                alt="{{ $event->name }}"
                class="mb-8 w-full max-w-2xl rounded-2xl border border-[#e3e3e0] object-cover sm:mx-0 mx-auto dark:border-[#3E3E3A]"
            >
        @endif

        <div class="max-w-2xl text-center sm:text-left">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                {{ $event->name }}
            </h1>
            <p class="mt-3 text-base text-[#706f6c] dark:text-[#A1A09A]">
                {{ $event->date->timezone(config('app.timezone'))->format('l, j F Y · H:i') }}
            </p>
            @if (filled($event->ticket_url))
                <div class="mt-6">
                    <a
                        href="{{ $event->ticket_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-sm border border-[#19140035] px-5 py-2 text-sm font-medium transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                    >
                        Get tickets
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                </div>
            @endif
            <div class="mt-6 whitespace-pre-line text-base leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ $event->description }}
            </div>
        </div>

        @if ($event->participants->isNotEmpty())
            <section class="mt-14" aria-labelledby="artists-heading">
                <h2 id="artists-heading" class="text-2xl font-semibold tracking-tight">
                    Artists
                </h2>

                <ul class="mt-8 grid grid-cols-[repeat(auto-fill,minmax(14rem,16rem))] justify-start gap-4">
                    @foreach ($event->participants as $participant)
                        @php $artist = $participant->artist; @endphp
                        <li
                            id="participant-{{ $participant->id }}"
                            class="flex h-full w-full flex-col overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]"
                        >
                            @if ($artist?->imageUrl())
                                <img
                                    src="{{ $artist->imageUrl() }}"
                                    alt="{{ $artist->name }}"
                                    class="aspect-square w-full object-cover"
                                >
                            @else
                                <div class="flex aspect-square w-full items-center justify-center bg-[#FDFDFC] text-[#706f6c] dark:bg-[#0a0a0a] dark:text-[#A1A09A]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="flex flex-1 flex-col gap-2 p-5">
                                <div>
                                    <h3 class="text-lg font-medium">{{ $artist?->name }}</h3>
                                    @if ($artist?->participantType)
                                        <p class="mt-0.5 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                            {{ $artist->participantType->name }}
                                        </p>
                                    @endif
                                    @if ($artist?->genres?->isNotEmpty())
                                        <ul class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach ($artist->genres as $genre)
                                                <li class="rounded-full border border-[#e3e3e0] px-2 py-0.5 text-xs text-[#706f6c] dark:border-[#3E3E3A] dark:text-[#A1A09A]">
                                                    {{ $genre->name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                @if (filled($artist?->bio))
                                    <p class="text-sm leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]">
                                        {{ $artist->bio }}
                                    </p>
                                @endif

                                @if ($participant->scheduleEntries->isNotEmpty())
                                    <ul class="mt-auto space-y-2 border-t border-[#e3e3e0] pt-3 dark:border-[#3E3E3A]">
                                        @foreach ($participant->scheduleEntries as $entry)
                                            <li>
                                                <a
                                                    href="#schedule-entry-{{ $entry->id }}"
                                                    class="js-schedule-link block rounded-sm text-sm transition hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a]"
                                                    data-schedule-target="schedule-entry-{{ $entry->id }}"
                                                >
                                                    <span class="block font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                                        {{ $entry->starts_at->timezone(config('app.timezone'))->format('l') }}
                                                    </span>
                                                    <span class="block text-[#706f6c] dark:text-[#A1A09A]">
                                                        {{ $entry->starts_at->timezone(config('app.timezone'))->format('H:i') }}
                                                        @if ($entry->ends_at)
                                                            – {{ $entry->ends_at->timezone(config('app.timezone'))->format('H:i') }}
                                                        @endif
                                                        @if ($entry->stage)
                                                            · {{ $entry->stage->name }}
                                                        @endif
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($scheduleByStage->isNotEmpty())
            <section id="schedule" class="mt-14" aria-labelledby="schedule-heading">
                <h2 id="schedule-heading" class="text-2xl font-semibold tracking-tight">
                    Schedule
                </h2>

                <div class="mt-8 space-y-10">
                    @php
                        $timezone = config('app.timezone');
                        $scheduleHasMultipleDays = $scheduleByStage
                            ->collapse()
                            ->map(fn ($entry) => $entry->starts_at?->timezone($timezone)->toDateString())
                            ->unique()
                            ->count() > 1;
                    @endphp
                    @foreach ($scheduleByStage as $entries)
                        @php
                            $stage = $entries->first()?->stage;
                            $entriesByDay = $entries->groupBy(
                                fn ($entry) => $entry->starts_at?->timezone($timezone)->toDateString() ?? ''
                            );
                        @endphp
                        <div>
                            <h3 class="text-lg font-medium">
                                {{ $stage?->name ?? 'Unassigned' }}
                            </h3>
                            <ol class="mt-4 divide-y divide-[#e3e3e0] overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white dark:divide-[#3E3E3A] dark:border-[#3E3E3A] dark:bg-[#161615]">
                                @foreach ($entriesByDay as $dayEntries)
                                    @if ($scheduleHasMultipleDays)
                                        <li class="bg-[#FDFDFC] px-5 py-2.5 dark:bg-[#0a0a0a]">
                                            <h4 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                                                {{ $dayEntries->first()?->starts_at?->timezone($timezone)->format('l, j F') }}
                                            </h4>
                                        </li>
                                    @endif
                                    @foreach ($dayEntries as $entry)
                                        <li
                                            id="schedule-entry-{{ $entry->id }}"
                                            class="schedule-entry scroll-mt-24 px-5 py-4 transition-[background-color,box-shadow] duration-300"
                                        >
                                            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:gap-4">
                                                <time
                                                    datetime="{{ $entry->starts_at->toIso8601String() }}"
                                                    class="shrink-0 text-sm font-medium tabular-nums text-[#706f6c] dark:text-[#A1A09A] sm:w-28"
                                                >
                                                    {{ $entry->starts_at->timezone($timezone)->format('H:i') }}
                                                    @if ($entry->ends_at)
                                                        – {{ $entry->ends_at->timezone($timezone)->format('H:i') }}
                                                    @endif
                                                </time>
                                                <div class="min-w-0 flex-1">
                                                    <a
                                                        href="#participant-{{ $entry->eventParticipant?->id }}"
                                                        class="font-medium text-[#1b1b18] transition hover:underline dark:text-[#EDEDEC]"
                                                    >
                                                        {{ $entry->eventParticipant?->artist?->name }}
                                                    </a>
                                                    @if (filled($entry->notes))
                                                        <p class="mt-0.5 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                                            {{ $entry->notes }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ol>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
@endsection
