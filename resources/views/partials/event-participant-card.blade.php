@php
    $isExtra = $participant->isExtra();
    $profile = $isExtra ? $participant->extra : $participant->artist;
    $bio = $profile?->localizedBio();
    $typeName = $isExtra
        ? $profile?->extraType?->localizedName()
        : $profile?->participantType?->localizedName();
    $meta = collect([$typeName])
        ->merge($isExtra ? [] : ($profile?->genres?->pluck('name') ?? []))
        ->filter();
    $links = collect([
        ['url' => $profile?->instagram_url, 'icon' => 'instagram', 'label' => 'Instagram'],
        ['url' => $profile?->website_url, 'icon' => 'website', 'label' => 'Website'],
    ])->filter(fn (array $link) => filled($link['url']));
@endphp
<li
    id="participant-{{ $participant->id }}"
    class="flex h-full w-full scroll-mt-24 flex-col overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition-[border-color,box-shadow] duration-300 dark:border-[#3E3E3A] dark:bg-[#161615]"
>
    @if ($profile?->imageUrl())
        <img
            src="{{ $profile->imageUrl() }}"
            alt="{{ $profile->name }}"
            class="aspect-square w-full object-cover"
        >
    @else
        <div class="flex aspect-square w-full items-center justify-center bg-[#FDFDFC] text-[#706f6c] dark:bg-[#0a0a0a] dark:text-[#A1A09A]">
            @if ($isExtra)
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.955 0 1.8-.458 2.346-1.164a3.004 3.004 0 0 0 3.154.164M3.75 9.349a3 3 0 0 1-.425-1.591V5.25A2.25 2.25 0 0 1 5.575 3h12.85A2.25 2.25 0 0 1 20.675 5.25v2.508c0 .576-.15 1.118-.425 1.591" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            @endif
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <h3 class="text-lg font-medium leading-snug">{{ $profile?->name }}</h3>
                @if ($meta->isNotEmpty())
                    <p class="mt-1 text-sm leading-snug text-[#706f6c] dark:text-[#A1A09A]">
                        {{ $meta->implode(' · ') }}
                    </p>
                @endif
            </div>

            @if ($links->isNotEmpty())
                <ul class="-mr-1.5 flex shrink-0">
                    @foreach ($links as $link)
                        <li>
                            <a
                                href="{{ $link['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ __($link['label']) }}"
                                title="{{ __($link['label']) }}"
                                class="inline-flex size-7 items-center justify-center rounded-md text-[#706f6c] transition hover:bg-[#FDFDFC] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:bg-[#0a0a0a] dark:hover:text-[#EDEDEC]"
                            >
                                @include('partials.social-icon', ['icon' => $link['icon'], 'class' => 'size-4'])
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if (filled($bio))
            <p class="text-sm leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ $bio }}
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
                                {{ $entry->date?->translatedFormat('l') }}
                            </span>
                            <span class="block text-[#706f6c] dark:text-[#A1A09A]">
                                @unless ($entry->isAllDay())
                                    {{ $entry->starts_at->format('H:i') }}
                                    @if ($entry->ends_at)
                                        – {{ $entry->ends_at->format('H:i') }}
                                    @endif
                                    @if ($entry->stage)
                                        ·
                                    @endif
                                @endunless
                                {{ $entry->stage?->localizedName() }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</li>
