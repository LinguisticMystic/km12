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
                {{ localized_date($event->date->timezone(config('app.timezone')), 'l, j F Y · H:i') }}
            </p>
            @if (filled($event->ticket_url))
                <div class="mt-6">
                    <a
                        href="{{ $event->ticket_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-sm border border-[#19140035] px-5 py-2 text-sm font-medium transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                    >
                        {{ __('Get tickets') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                </div>
            @endif
            <div class="mt-6 whitespace-pre-line text-base leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ $event->localizedDescription() }}
            </div>
        </div>

        @if ($scheduleByDay->isNotEmpty())
            <section id="schedule" class="mt-14" aria-labelledby="schedule-heading">
                <h2 id="schedule-heading" class="text-2xl font-semibold tracking-tight">
                    {{ __('Schedule') }}
                </h2>

                <ol class="mt-8 divide-y divide-[#e3e3e0] overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white dark:divide-[#3E3E3A] dark:border-[#3E3E3A] dark:bg-[#161615]">
                    @foreach ($scheduleByDay as $dayEntries)
                        @if ($dayEntries->first()?->date)
                            <li class="bg-[#FDFDFC] px-5 py-2.5 dark:bg-[#0a0a0a]">
                                <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ localized_date($dayEntries->first()?->date, 'l, j F') }}
                                </h3>
                            </li>
                        @endif
                        @foreach ($dayEntries as $entry)
                            <li
                                id="schedule-entry-{{ $entry->id }}"
                                class="schedule-entry scroll-mt-24 px-5 py-4 transition-[background-color,box-shadow] duration-300"
                            >
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:gap-4">
                                    @if ($entry->isAllDay())
                                        <span class="shrink-0 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] sm:w-28">
                                            {{ __('All day') }}
                                        </span>
                                    @else
                                        <time
                                            datetime="{{ $entry->starts_at->format('H:i') }}"
                                            class="shrink-0 text-sm font-medium tabular-nums text-[#706f6c] dark:text-[#A1A09A] sm:w-28"
                                        >
                                            {{ $entry->starts_at->format('H:i') }}
                                            @if ($entry->ends_at)
                                                – {{ $entry->ends_at->format('H:i') }}
                                            @endif
                                        </time>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-baseline gap-x-2.5 gap-y-1">
                                            <a
                                                href="#participant-{{ $entry->eventParticipant?->id }}"
                                                class="font-medium text-[#1b1b18] transition hover:underline dark:text-[#EDEDEC]"
                                            >
                                                {{ $entry->eventParticipant?->displayName() }}
                                            </a>
                                            @if ($entry->stage)
                                                <span class="inline-flex items-center rounded-full border border-[#e3e3e0] px-2 py-0.5 text-xs font-medium text-[#706f6c] dark:border-[#3E3E3A] dark:text-[#A1A09A]">
                                                    {{ $entry->stage->name }}
                                                </span>
                                            @endif
                                        </div>
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
            </section>
        @endif

        @if ($event->artistParticipants->isNotEmpty())
            <section class="mt-14" aria-labelledby="artists-heading">
                <h2 id="artists-heading" class="text-2xl font-semibold tracking-tight">
                    {{ __('Artists') }}
                </h2>

                <ul class="mt-8 grid grid-cols-[repeat(auto-fill,minmax(14rem,16rem))] justify-center gap-4 sm:justify-start">
                    @foreach ($event->artistParticipants as $participant)
                        @include('partials.event-participant-card', ['participant' => $participant])
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($event->extraParticipants->isNotEmpty())
            <section class="mt-14" aria-labelledby="extras-heading">
                <h2 id="extras-heading" class="text-2xl font-semibold tracking-tight">
                    {{ __('Extras') }}
                </h2>

                <ul class="mt-8 grid grid-cols-[repeat(auto-fill,minmax(14rem,16rem))] justify-center gap-4 sm:justify-start">
                    @foreach ($event->extraParticipants as $participant)
                        @include('partials.event-participant-card', ['participant' => $participant])
                    @endforeach
                </ul>
            </section>
        @endif
    </article>
@endsection
