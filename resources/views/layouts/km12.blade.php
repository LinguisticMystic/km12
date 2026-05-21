<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'KM12')</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('apple-touch-icon.png') }}" type="image/png" sizes="512x512">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] antialiased dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <header class="absolute inset-x-0 top-0 z-10 p-6 sm:p-8">
            <div class="mx-auto flex max-w-4xl items-start justify-between gap-6">
                @include('partials.breadcrumbs')

                <nav class="ml-auto flex shrink-0 items-center gap-4 text-sm">
                @auth
                    @if (auth()->user()->is_admin)
                        <a
                            href="{{ url('/admin') }}"
                            class="rounded-sm border border-[#19140035] px-5 py-1.5 transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                        >
                            Admin
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="cursor-pointer rounded-sm border border-[#19140035] px-5 py-1.5 transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                        >
                            Log out
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="rounded-sm border border-[#19140035] px-5 py-1.5 transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                    >
                        Log in
                    </a>
                @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto flex w-full max-w-4xl flex-1 flex-col items-center justify-center px-6 py-24 sm:px-8">
            @yield('content')
        </main>

        <footer class="px-6 py-8 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
            Created by
            <a
                href="https://github.com/LinguisticMystic"
                target="_blank"
                rel="noopener noreferrer"
                class="font-medium text-[#1b1b18] underline decoration-[#e3e3e0] underline-offset-2 transition hover:decoration-[#19140035] dark:text-[#EDEDEC] dark:decoration-[#3E3E3A] dark:hover:decoration-[#62605b]"
            >
                LinguisticMystic
            </a>
        </footer>
    </body>
</html>
