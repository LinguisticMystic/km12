@php
    $icon = $icon ?? 'link';
@endphp

<svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
    @switch ($icon)
        @case('website')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A8.966 8.966 0 0 1 3 12c0-1.264.26-2.467.732-3.553" />
            @break
        @case('email')
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            @break
        @case('facebook')
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
            @break
        @case('instagram')
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 2H8a6 6 0 0 0-6 6v8a6 6 0 0 0 6 6h8a6 6 0 0 0 6-6V8a6 6 0 0 0-6-6Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.5 6.5h.01" />
            @break
        @case('discord')
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5c.94.94 2.48.94 3.42 0M13.5 10.5c.94.94 2.48.94 3.42 0M7.5 15.5h9M6 18c3.5-1 8.5-1 12 0M6 6c2.5 2 5.5 3 9 3s6.5-1 9-3" />
            @break
        @case('telegram')
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 14-7-4 14-2-5-7-5" />
            @break
        @case('youtube')
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 10.5 18 12l-3.75 1.5V10.5ZM6 18h12a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v6a3 3 0 0 0 3 3Z" />
            @break
        @default
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
    @endswitch
</svg>
