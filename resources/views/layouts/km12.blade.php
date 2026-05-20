<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'KM12')</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] antialiased dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <header class="absolute top-0 right-0 p-6 sm:p-8">
            <nav class="flex items-center gap-4 text-sm">
                <a
                    href="{{ route('home') }}"
                    class="rounded-sm border border-transparent px-5 py-1.5 transition hover:border-[#19140035] dark:hover:border-[#3E3E3A]"
                >
                    Home
                </a>

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
        </header>

        <main class="mx-auto flex min-h-screen max-w-4xl flex-col items-center justify-center px-6 py-24 sm:px-8">
            @yield('content')
        </main>
    </body>
</html>
