@extends('layouts.km12')

@section('title', 'Door opener — KM12')

@section('content')
    <div class="w-full text-center">
        <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
            Door opener
        </h1>
        <p class="mx-auto mt-4 max-w-md text-base text-[#706f6c] dark:text-[#A1A09A]">
            Open the door or gate remotely. Your request is usually handled within a few seconds.
        </p>
    </div>

    <style>
        .door-opener-icon {
            --cycle-ms: 1400ms;
            --gate-slide: 6px;
            --door-slide: 9px;
        }

        .door-opener-icon [data-leaf],
        .door-opener-icon [data-panel] {
            transform-box: fill-box;
        }

        @keyframes door-opener-gate-left {
            0%, 100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(calc(-1 * var(--gate-slide)));
            }
        }

        @keyframes door-opener-gate-right {
            0%, 100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(var(--gate-slide));
            }
        }

        @keyframes door-opener-door-panel {
            0%, 100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(calc(-1 * var(--door-slide)));
            }
        }

        .door-opener-icon--opening [data-leaf="left"],
        .door-opener-icon--open [data-leaf="left"] {
            animation: door-opener-gate-left var(--cycle-ms) ease-in-out infinite;
        }

        .door-opener-icon--opening [data-leaf="right"],
        .door-opener-icon--open [data-leaf="right"] {
            animation: door-opener-gate-right var(--cycle-ms) ease-in-out infinite;
        }

        .door-opener-icon--opening [data-panel],
        .door-opener-icon--open [data-panel] {
            animation: door-opener-door-panel var(--cycle-ms) ease-in-out infinite;
        }

        .door-opener-progress {
            pointer-events: none;
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 3px;
            overflow: hidden;
            border-radius: 0 0 1rem 1rem;
            background: rgb(25 20 0 / 6%);
            opacity: 0;
            transition: opacity 150ms ease;
        }

        .dark .door-opener-progress {
            background: rgb(237 237 236 / 8%);
        }

        .door-opener-control.is-progressing .door-opener-progress {
            opacity: 1;
        }

        .door-opener-control.is-progressing .door-opener-progress__fill {
            transform: scaleX(1);
            transition: none;
        }

        .door-opener-progress__fill {
            display: block;
            height: 100%;
            width: 100%;
            transform: scaleX(1);
            transform-origin: left center;
            background: #1b1b18;
        }

        .dark .door-opener-progress__fill {
            background: #EDEDEC;
        }

        .door-opener-control.is-progressing-active .door-opener-progress__fill {
            transform: scaleX(0);
            transition: transform var(--progress-ms, 10s) linear;
        }

        @media (prefers-reduced-motion: reduce) {
            .door-opener-icon--opening [data-leaf="left"],
            .door-opener-icon--open [data-leaf="left"] {
                animation: none;
                transform: translateX(calc(-1 * var(--gate-slide)));
            }

            .door-opener-icon--opening [data-leaf="right"],
            .door-opener-icon--open [data-leaf="right"] {
                animation: none;
                transform: translateX(var(--gate-slide));
            }

            .door-opener-icon--opening [data-panel],
            .door-opener-icon--open [data-panel] {
                animation: none;
                transform: translateX(calc(-1 * var(--door-slide)));
            }

            .door-opener-control.is-progressing-active .door-opener-progress__fill {
                transition-duration: 0.01ms;
            }
        }
    </style>

    <div class="mt-14 grid w-full max-w-lg grid-cols-2 gap-4 sm:gap-6">
        <button
            type="button"
            id="open-door"
            data-action="door"
            aria-label="Open door"
            class="door-opener-control group relative flex aspect-square cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white p-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 sm:gap-4 sm:p-6 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="door-opener-icon door-opener-icon--closed flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 group-disabled:scale-100 sm:size-16 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 sm:size-10" fill="none" viewBox="0 0 32 32" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h24v24H4z" opacity="0.35" />
                    <g data-panel>
                        <rect x="14" y="5" width="13" height="22" rx="1" fill="currentColor" fill-opacity="0.12" stroke="currentColor" />
                        <circle cx="23" cy="16" r="1.25" fill="currentColor" stroke="none" />
                    </g>
                </svg>
            </span>
            <span class="flex min-h-10 items-center justify-center px-1 text-center text-sm font-medium leading-tight sm:min-h-0 sm:text-lg">Open door</span>
            <span class="door-opener-progress" aria-hidden="true"><span class="door-opener-progress__fill"></span></span>
        </button>

        <button
            type="button"
            id="open-gate"
            data-action="gate"
            aria-label="Open gate"
            class="door-opener-control group relative flex aspect-square cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-2xl border border-[#e3e3e0] bg-white p-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] transition hover:border-[#19140035] hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 sm:gap-4 sm:p-6 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#62605b]"
        >
            <span class="door-opener-icon door-opener-icon--closed flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#FDFDFC] text-[#1b1b18] transition group-hover:scale-105 group-disabled:scale-100 sm:size-16 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 sm:size-10" fill="none" viewBox="0 0 32 32" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 27h26" />
                    <g data-leaf="left">
                        <rect x="3" y="6" width="12" height="20" rx="1" fill="currentColor" fill-opacity="0.12" stroke="currentColor" />
                        <line x1="6" y1="8" x2="6" y2="24" stroke-width="1" opacity="0.55" />
                        <line x1="9" y1="8" x2="9" y2="24" stroke-width="1" opacity="0.55" />
                        <line x1="12" y1="8" x2="12" y2="24" stroke-width="1" opacity="0.55" />
                    </g>
                    <g data-leaf="right">
                        <rect x="17" y="6" width="12" height="20" rx="1" fill="currentColor" fill-opacity="0.12" stroke="currentColor" />
                        <line x1="20" y1="8" x2="20" y2="24" stroke-width="1" opacity="0.55" />
                        <line x1="23" y1="8" x2="23" y2="24" stroke-width="1" opacity="0.55" />
                        <line x1="26" y1="8" x2="26" y2="24" stroke-width="1" opacity="0.55" />
                    </g>
                </svg>
            </span>
            <span class="flex min-h-10 items-center justify-center px-1 text-center text-sm font-medium leading-tight sm:min-h-0 sm:text-lg">Open gate</span>
            <span class="door-opener-progress" aria-hidden="true"><span class="door-opener-progress__fill"></span></span>
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
            const openSeconds = @json(config('door_opener.door_open_seconds'));
            const timings = {
                door: { openingMs: 600, openSeconds },
                gate: { openingMs: 600, openSeconds },
            };

            const statusEl = document.getElementById('door-opener-status');
            const controls = document.querySelectorAll('.door-opener-control');
            let busy = false;
            let resetTimer = null;

            function setStatus(message, isError = false) {
                statusEl.textContent = message;
                statusEl.classList.toggle('text-red-600', isError);
                statusEl.classList.toggle('dark:text-red-400', isError);
            }

            function iconFor(button) {
                return button.querySelector('.door-opener-icon');
            }

            function setIconState(button, state) {
                const icon = iconFor(button);
                if (!icon) {
                    return;
                }

                icon.classList.remove('door-opener-icon--closed', 'door-opener-icon--opening', 'door-opener-icon--open');
                icon.classList.add(`door-opener-icon--${state}`);
            }

            function setControlsDisabled(disabled) {
                controls.forEach((btn) => {
                    btn.disabled = disabled;
                });
            }

            function resetAllIcons() {
                controls.forEach((btn) => setIconState(btn, 'closed'));
            }

            function stopProgress(button) {
                button.classList.remove('is-progressing', 'is-progressing-active');
                button.style.removeProperty('--progress-ms');
            }

            function stopAllProgress() {
                controls.forEach(stopProgress);
            }

            function startProgress(button, durationMs) {
                stopProgress(button);
                button.style.setProperty('--progress-ms', `${durationMs}ms`);
                button.classList.add('is-progressing');
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        button.classList.add('is-progressing-active');
                    });
                });
            }

            function wait(ms) {
                return new Promise((resolve) => window.setTimeout(resolve, ms));
            }

            async function trigger(action) {
                if (busy) {
                    return;
                }

                const button = document.querySelector(`[data-action="${action}"]`);
                const timing = timings[action];

                if (!button || !timing) {
                    return;
                }

                busy = true;
                setControlsDisabled(true);
                setStatus('');
                setIconState(button, 'opening');

                const openingDone = wait(timing.openingMs);
                const requestDone = fetch(routes[action], {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                }).then(async (response) => {
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(data.message || 'Request failed.');
                    }

                    return data;
                });

                try {
                    const [, data] = await Promise.all([openingDone, requestDone]);
                    const openSeconds = Number(data.open_seconds) > 0
                        ? Number(data.open_seconds)
                        : timing.openSeconds;

                    setIconState(button, 'open');
                    startProgress(button, openSeconds * 1000);
                    await wait(openSeconds * 1000);
                } catch (error) {
                    setStatus(error.message || 'Something went wrong.', true);
                } finally {
                    resetAllIcons();
                    stopAllProgress();
                    busy = false;
                    setControlsDisabled(false);

                    if (resetTimer !== null) {
                        window.clearTimeout(resetTimer);
                    }

                    resetTimer = window.setTimeout(() => {
                        if (!busy && statusEl.textContent !== '') {
                            setStatus('');
                        }
                    }, 4000);
                }
            }

            controls.forEach((btn) => {
                btn.addEventListener('click', () => trigger(btn.dataset.action));
            });
        })();
    </script>
@endsection
