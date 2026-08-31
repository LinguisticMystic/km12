@extends('layouts.km12')

@section('title', $gallery->name.' — KM12')

@section('content')
    <article class="w-full max-w-4xl self-stretch" data-gallery>
        <div class="gallery-heading text-center sm:text-left">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                {{ $gallery->name }}
            </h1>
        </div>

        @if ($gallery->images->isEmpty())
            <p class="gallery-empty mt-12 text-center text-sm text-[#706f6c] sm:text-left dark:text-[#A1A09A]">
                {{ __('No photos in this gallery yet.') }}
            </p>
        @else
            <ul class="gallery-grid mt-12 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                @foreach ($gallery->images as $image)
                    <li>
                        <button
                            type="button"
                            class="gallery-thumb group block w-full overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white text-left shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition-colors hover:border-[#19140035] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b] dark:focus-visible:ring-[#EDEDEC]"
                            data-gallery-open
                            data-src="{{ $image->url() }}"
                            data-alt="{{ $image->annotation ?: $gallery->name }}"
                            data-caption="{{ $image->annotation }}"
                            aria-haspopup="dialog"
                        >
                            <img
                                src="{{ $image->url() }}"
                                alt="{{ $image->annotation ?: $gallery->name }}"
                                loading="lazy"
                                decoding="async"
                                class="aspect-square w-full object-cover"
                            >
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif

        <dialog
            data-gallery-dialog
            class="gallery-dialog m-auto w-[min(100%-2rem,64rem)] max-h-[90vh] overflow-auto rounded-2xl border border-[#e3e3e0] bg-white p-4 shadow-xl backdrop:bg-black/70 dark:border-[#3E3E3A] dark:bg-[#161615]"
            aria-label="{{ __('Photo') }}"
        >
            <div class="gallery-toolbar flex justify-end">
                <button
                    type="button"
                    data-gallery-close
                    class="rounded-sm border border-[#19140035] px-3 py-1 text-sm transition-colors hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                >
                    {{ __('Close') }}
                </button>
            </div>
            <div class="gallery-stage relative mt-3">
                <button
                    type="button"
                    data-gallery-prev
                    class="gallery-nav absolute top-1/2 left-2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-[#19140035] bg-white/90 text-[#1b1b18] shadow-sm transition-colors hover:border-[#1915014a] dark:border-[#3E3E3A] dark:bg-[#161615]/90 dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    aria-label="{{ __('Previous photo') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <img alt="" class="max-h-[75vh] w-full rounded-xl object-contain">
                <button
                    type="button"
                    data-gallery-next
                    class="gallery-nav absolute top-1/2 right-2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-[#19140035] bg-white/90 text-[#1b1b18] shadow-sm transition-colors hover:border-[#1915014a] dark:border-[#3E3E3A] dark:bg-[#161615]/90 dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    aria-label="{{ __('Next photo') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
            <p data-gallery-caption class="mt-3 text-sm text-[#706f6c] dark:text-[#A1A09A]" hidden></p>
        </dialog>
    </article>
@endsection
