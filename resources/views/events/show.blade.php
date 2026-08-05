@extends('layouts.km12')

@section('title', $event->name.' — KM12')

@section('content')
    <article class="w-full max-w-2xl self-stretch">
        @if ($event->posterUrl())
            <img
                src="{{ $event->posterUrl() }}"
                alt="{{ $event->name }}"
                class="mb-8 w-full rounded-2xl border border-[#e3e3e0] object-cover dark:border-[#3E3E3A]"
            >
        @endif

        <div class="text-center sm:text-left">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                {{ $event->name }}
            </h1>
            <p class="mt-3 text-base text-[#706f6c] dark:text-[#A1A09A]">
                {{ $event->date->timezone(config('app.timezone'))->format('l, j F Y · H:i') }}
            </p>
            <div class="mt-6 whitespace-pre-line text-base leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ $event->description }}
            </div>
        </div>

        @if (filled($event->ticket_url))
            <div class="mt-10 text-center sm:text-left">
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
    </article>
@endsection
