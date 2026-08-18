@php
    $currentLocale = app()->getLocale();
@endphp

<div class="flex items-center gap-1" role="group" aria-label="{{ __('Language') }}">
    @foreach (config('app.available_locales', ['lv', 'en']) as $locale)
        @php $label = strtoupper($locale); @endphp
        @if ($currentLocale === $locale)
            <span
                aria-current="true"
                class="rounded-sm border border-[#19140035] px-2.5 py-1.5 font-medium dark:border-[#3E3E3A]"
            >
                {{ $label }}
            </span>
        @else
            <form method="POST" action="{{ route('locale.update') }}" class="inline">
                @csrf
                <input type="hidden" name="locale" value="{{ $locale }}">
                <button
                    type="submit"
                    class="cursor-pointer rounded-sm border border-transparent px-2.5 py-1.5 text-[#706f6c] transition hover:border-[#19140035] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:border-[#3E3E3A] dark:hover:text-[#EDEDEC]"
                >
                    {{ $label }}
                </button>
            </form>
        @endif
    @endforeach
</div>
