@extends('layouts.km12')

@section('title', 'KM12')

@section('content')
    <div class="w-full text-center">
        <h1 class="text-7xl font-semibold tracking-tight sm:text-8xl md:text-9xl">
            KM12
        </h1>
        <p class="mx-auto mt-4 max-w-md text-base text-[#706f6c] dark:text-[#A1A09A] sm:text-lg">
            Tools for members of the KM12 community — pick one below to get started.
        </p>
    </div>

    <div class="mt-14 grid w-full max-w-lg grid-cols-2 gap-4 sm:gap-6">
        <a
            href="{{ route('door-opener') }}"
            class="group flex aspect-square flex-col items-center justify-center gap-4 rounded-2xl border border-[#e3e3e0] bg-white p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-14 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m0 0h6m-6 0h6" />
                </svg>
            </span>
            <span class="text-center text-lg font-medium">Door opener</span>
        </a>

        <a
            href="{{ route('wayfinder') }}"
            class="group flex aspect-square flex-col items-center justify-center gap-4 rounded-2xl border border-[#e3e3e0] bg-white p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-14 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </span>
            <span class="text-center text-lg font-medium">Wayfinder</span>
        </a>
    </div>
@endsection
