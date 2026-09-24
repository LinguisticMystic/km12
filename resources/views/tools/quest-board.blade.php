@extends('layouts.km12')

@section('title', __('Quest board').' — KM12')

@section('content')
    <div class="w-full max-w-2xl self-stretch">
        <div class="text-center sm:text-left">
            <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                {{ __('Quest board') }}
            </h1>
        </div>

        @if ($quests->isEmpty())
            <p class="mt-12 text-center text-sm text-[#706f6c] sm:text-left dark:text-[#A1A09A]">
                {{ __('No quests yet. Check back soon.') }}
            </p>
        @else
            <ul class="mt-12 space-y-4">
                @foreach ($quests as $quest)
                    @php
                        $visibleStatus = $quest->statusVisibleTo(auth()->user());
                    @endphp
                    <li class="rounded-2xl border border-[#e3e3e0] bg-white px-5 py-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div class="flex items-start justify-between gap-4">
                            <h2 class="font-medium">{{ $quest->name }}</h2>
                            <span class="shrink-0 rounded-full bg-[#FDFDFC] px-2.5 py-1 text-sm font-medium text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                                {{ __(':count XP', ['count' => $quest->experience_points]) }}
                            </span>
                        </div>
                        <p class="mt-2">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                                'bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' => $visibleStatus === \App\Enums\QuestStatus::Available,
                                'bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-200' => $visibleStatus === \App\Enums\QuestStatus::InProgress,
                                'bg-sky-50 text-sky-800 dark:bg-sky-950 dark:text-sky-200' => $visibleStatus === \App\Enums\QuestStatus::Submitted,
                                'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200' => $visibleStatus === \App\Enums\QuestStatus::Completed,
                                'bg-red-50 text-red-800 dark:bg-red-950 dark:text-red-200' => $visibleStatus === \App\Enums\QuestStatus::Rejected,
                            ])>
                                {{ $visibleStatus->label() }}
                            </span>
                        </p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $quest->description }}
                        </p>
                        @if ($quest->creator)
                            <p class="mt-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ __('Created by :name', ['name' => $quest->creator->name]) }}
                            </p>
                        @endif
                        @if ($quest->taker && $visibleStatus !== \App\Enums\QuestStatus::Available)
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ __('Taken by :name', ['name' => $quest->taker->name]) }}
                            </p>
                        @endif
                        @can('start', $quest)
                            <form method="POST" action="{{ route('quests.start', $quest) }}" class="mt-4">
                                @csrf
                                <button
                                    type="submit"
                                    class="cursor-pointer rounded-sm border border-[#19140035] px-5 py-1.5 text-sm transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                                >
                                    {{ __('Start quest') }}
                                </button>
                            </form>
                        @endcan
                        @can('submit', $quest)
                            <form method="POST" action="{{ route('quests.submit', $quest) }}" class="mt-4">
                                @csrf
                                <button
                                    type="submit"
                                    class="cursor-pointer rounded-sm border border-[#19140035] px-5 py-1.5 text-sm transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                                >
                                    {{ __('Submit quest') }}
                                </button>
                            </form>
                        @endcan
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
