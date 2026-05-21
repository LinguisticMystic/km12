@extends('layouts.km12')

@section('title', 'Calendar — KM12')

@section('content')
    <div class="w-full self-stretch">
        <div class="w-full text-center">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                Calendar
            </h1>
            <p class="mx-auto mt-4 max-w-md text-base text-[#706f6c] dark:text-[#A1A09A]">
                Community events and schedules (Europe/Riga).
            </p>
        </div>

        <div class="mx-auto mt-10 w-full max-w-3xl overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]">
            <div class="aspect-[4/3] w-full">
                <iframe
                    src="https://calendar.google.com/calendar/embed?src=c_2fa76f6afdd28b5e5d0af6f9c0455248c617deb73e06c8af0e73b2df5a0c311a%40group.calendar.google.com&ctz=Europe%2FRiga"
                    title="KM12 community calendar"
                    class="h-full w-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </div>
@endsection
