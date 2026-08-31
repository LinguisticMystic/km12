@extends('layouts.km12')

@section('title', __('Galleries').' — KM12')

@section('content')
    <div class="w-full max-w-2xl self-stretch">
        <div class="text-center sm:text-left">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                {{ __('Galleries') }}
            </h1>
            <p class="mt-4 text-base leading-relaxed text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('Photos from KM12 events and gatherings.') }}
            </p>
        </div>

        @if ($galleries->isEmpty())
            <p class="mt-12 text-center text-sm text-[#706f6c] sm:text-left dark:text-[#A1A09A]">
                {{ __('No galleries yet. Check back soon.') }}
            </p>
        @else
            <ul class="mt-12 divide-y divide-[#e3e3e0] overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:divide-[#3E3E3A] dark:border-[#3E3E3A] dark:bg-[#161615]">
                @foreach ($galleries as $gallery)
                    <li>
                        <a
                            href="{{ route('galleries.show', $gallery) }}"
                            class="group flex items-center gap-4 px-5 py-4 transition hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a]"
                        >
                            @if ($gallery->coverImage?->url())
                                <img
                                    src="{{ $gallery->coverImage->url() }}"
                                    alt=""
                                    class="size-14 shrink-0 rounded-xl object-cover"
                                >
                            @else
                                <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5V6A1.5 1.5 0 0 0 20.25 4.5H3.75A1.5 1.5 0 0 0 2.25 6v13.5A1.5 1.5 0 0 0 3.75 21Zm10.5-11.25h.008v.008h-.008V9.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </span>
                            @endif
                            <span class="min-w-0 flex-1">
                                <span class="block font-medium">{{ $gallery->name }}</span>
                                <span class="mt-0.5 block text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $gallery->images_count === 1 ? __('1 photo') : __(':count photos', ['count' => $gallery->images_count]) }}
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
