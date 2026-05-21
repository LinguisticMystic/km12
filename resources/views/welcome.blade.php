@extends('layouts.km12')

@section('title', 'KM12')

@section('content')
    <div class="w-full text-center">
        <h1 class="text-7xl font-semibold tracking-tight sm:text-8xl md:text-9xl">
            KM12
        </h1>
        <p class="mx-auto mt-4 max-w-md text-base text-[#706f6c] dark:text-[#A1A09A] sm:text-lg">
            Tools and resources for members and visitors of the KM12 community.
        </p>
    </div>

    <div class="mt-14 grid w-full max-w-lg grid-cols-2 gap-4 sm:gap-6">
        <a
            href="{{ route('door-opener') }}"
            class="group flex aspect-square flex-col items-center justify-center gap-2 rounded-2xl border border-[#e3e3e0] bg-white p-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md sm:gap-4 sm:p-6 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 sm:size-14 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-7 sm:size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m0 0h6m-6 0h6" />
                </svg>
            </span>
            <span class="flex min-h-10 items-center justify-center px-1 text-center text-sm font-medium leading-tight sm:min-h-0 sm:text-lg">Door opener</span>
        </a>

        <a
            href="{{ route('calendar') }}"
            class="group flex aspect-square flex-col items-center justify-center gap-2 rounded-2xl border border-[#e3e3e0] bg-white p-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md sm:gap-4 sm:p-6 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 sm:size-14 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-7 sm:size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </span>
            <span class="flex min-h-10 items-center justify-center px-1 text-center text-sm font-medium leading-tight sm:min-h-0 sm:text-lg">Calendar</span>
        </a>

        <a
            href="{{ route('wayfinder') }}"
            class="group flex aspect-square flex-col items-center justify-center gap-2 rounded-2xl border border-[#e3e3e0] bg-white p-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md sm:gap-4 sm:p-6 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 sm:size-14 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-7 sm:size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </span>
            <span class="flex min-h-10 items-center justify-center px-1 text-center text-sm font-medium leading-tight sm:min-h-0 sm:text-lg">Wayfinder</span>
        </a>

        <a
            href="{{ route('about') }}"
            class="group flex aspect-square flex-col items-center justify-center gap-2 rounded-2xl border border-[#e3e3e0] bg-white p-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md sm:gap-4 sm:p-6 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 sm:size-14 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-7 sm:size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </span>
            <span class="flex min-h-10 items-center justify-center px-1 text-center text-sm font-medium leading-tight sm:min-h-0 sm:text-lg">About</span>
        </a>
    </div>
@endsection
