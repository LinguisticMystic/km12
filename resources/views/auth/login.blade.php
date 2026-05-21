@extends('layouts.km12')

@section('title', 'Log in — KM12')

@section('content')
    <div class="w-full max-w-md">
        <h1 class="text-center text-3xl font-semibold tracking-tight sm:text-4xl">
            Log in
        </h1>
        <p class="mt-3 text-center text-base text-[#706f6c] dark:text-[#A1A09A]">
            Some tools are only available to signed-in members. Sign in with the account created for you — new accounts are created by administrators only.
        </p>

        <form method="POST" action="{{ route('login') }}" class="mt-10 space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-2 w-full rounded-lg border border-[#e3e3e0] bg-white px-4 py-2.5 text-[#1b1b18] shadow-sm outline-none transition focus:border-[#19140035] dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:focus:border-[#62605b]"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-2 w-full rounded-lg border border-[#e3e3e0] bg-white px-4 py-2.5 text-[#1b1b18] shadow-sm outline-none transition focus:border-[#19140035] dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:focus:border-[#62605b]"
                >
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="rounded border-[#e3e3e0] dark:border-[#3E3E3A]"
                >
                Remember me
            </label>

            <button
                type="submit"
                class="w-full rounded-lg border border-[#19140035] bg-[#1b1b18] px-5 py-2.5 font-medium text-[#FDFDFC] transition hover:bg-black dark:border-[#3E3E3A] dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
            >
                Log in
            </button>
        </form>
    </div>
@endsection
