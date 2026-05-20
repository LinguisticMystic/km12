@extends('layouts.km12')

@section('title', 'Door opener — KM12')

@section('content')
    <div class="w-full text-center">
        <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
            Door opener
        </h1>
        <p class="mx-auto mt-4 max-w-md text-base text-[#706f6c] dark:text-[#A1A09A]">
            Open the door or gate remotely. The Arduino will pick up your request within a few seconds.
        </p>
    </div>

    <div class="mt-14 grid w-full max-w-lg grid-cols-2 gap-4 sm:gap-6">
        <button
            type="button"
            id="open-door"
            class="group flex aspect-square cursor-pointer flex-col items-center justify-center gap-4 rounded-2xl border border-[#e3e3e0] bg-white p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-14 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 group-disabled:scale-100 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m0 0h6m-6 0h6" />
                </svg>
            </span>
            <span class="text-center text-lg font-medium">Open door</span>
        </button>

        <button
            type="button"
            id="open-gate"
            class="group flex aspect-square cursor-pointer flex-col items-center justify-center gap-4 rounded-2xl border border-[#e3e3e0] bg-white p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="flex size-14 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 group-disabled:scale-100 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15m0 0v18m0-18-2.25 4.5M19.5 3 21 7.5M6 18h.008v.008H6V18Zm3 0h.008v.008H9V18Zm3 0h.008v.008H12V18Zm3 0h.008v.008H15V18Zm3 0h.008v.008H18V18Z" />
                </svg>
            </span>
            <span class="text-center text-lg font-medium">Open gate</span>
        </button>
    </div>

    <p
        id="door-opener-status"
        class="mt-8 min-h-[1.5rem] text-center text-sm text-[#706f6c] dark:text-[#A1A09A]"
        role="status"
        aria-live="polite"
    ></p>

    <script>
        (function () {
            const csrf = @json(csrf_token());
            const routes = {
                door: @json(route('door-opener.open-door')),
                gate: @json(route('door-opener.open-gate')),
            };

            const statusEl = document.getElementById('door-opener-status');
            const buttons = {
                door: document.getElementById('open-door'),
                gate: document.getElementById('open-gate'),
            };

            function setStatus(message, isError = false) {
                statusEl.textContent = message;
                statusEl.classList.toggle('text-red-600', isError);
                statusEl.classList.toggle('dark:text-red-400', isError);
            }

            function setLoading(loading) {
                Object.values(buttons).forEach((btn) => {
                    btn.disabled = loading;
                });
            }

            async function trigger(action) {
                setLoading(true);
                setStatus('Sending request…');

                try {
                    const response = await fetch(routes[action], {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(data.message || 'Request failed.');
                    }

                    setStatus(data.message || 'Request sent. Waiting for Arduino…');
                } catch (error) {
                    setStatus(error.message || 'Something went wrong.', true);
                } finally {
                    setLoading(false);
                }
            }

            buttons.door.addEventListener('click', () => trigger('door'));
            buttons.gate.addEventListener('click', () => trigger('gate'));
        })();
    </script>
@endsection
