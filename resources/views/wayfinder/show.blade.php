@extends('layouts.km12')

@section('title', 'Wayfinder — KM12')

@section('content')
    <div class="wayfinder w-full">
        <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
            Wayfinder
        </h1>
        <h2 class="mt-2 text-xl font-medium text-[#706f6c] dark:text-[#A1A09A]">
            1st floor
        </h2>

        <style>
            .wayfinder {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .wayfinder #map-container {
                position: relative;
                width: 100%;
                max-width: 816px;
                aspect-ratio: 595 / 842;
                margin-top: 1.5rem;
            }

            .wayfinder #map-container img {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                object-fit: fill;
                display: block;
            }

            .wayfinder #map-container svg.overlay {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
            }

            .wayfinder .room-trigger {
                cursor: pointer;
                transition: fill 0.2s;
                pointer-events: fill;
            }

            .wayfinder .room-purple {
                fill: rgba(142, 68, 173, 0.22);
            }

            .wayfinder .room-purple:hover {
                fill: rgba(142, 68, 173, 0.48);
            }

            .wayfinder .room-orange {
                fill: rgba(230, 126, 34, 0.22);
            }

            .wayfinder .room-orange:hover {
                fill: rgba(230, 126, 34, 0.48);
            }

            .wayfinder .room-teal {
                fill: rgba(0, 120, 140, 0.18);
            }

            .wayfinder .room-teal:hover {
                fill: rgba(0, 120, 140, 0.42);
            }

            .wayfinder .room-label {
                font-family: system-ui, -apple-system, sans-serif;
                font-weight: 700;
                pointer-events: none;
                user-select: none;
            }

            .wayfinder #path-line {
                fill: none;
                stroke: #FF0000;
                stroke-width: 4;
                stroke-linecap: round;
                stroke-linejoin: round;
                stroke-dasharray: 8;
                animation: wayfinder-dash 5s linear infinite;
            }

            @keyframes wayfinder-dash {
                to {
                    stroke-dashoffset: -100;
                }
            }

            .wayfinder #grid-status {
                font-size: 13px;
                color: #555;
                margin: 0.75rem 0 0;
                min-height: 1.2em;
            }

            .dark .wayfinder #grid-status {
                color: #A1A09A;
            }

            .wayfinder #room-list {
                width: 100%;
                max-width: 816px;
                margin-top: 1.25rem;
            }

            .wayfinder #room-list h3 {
                font-size: 0.875rem;
                font-weight: 600;
                color: #706f6c;
                margin: 0 0 0.5rem;
            }

            .dark .wayfinder #room-list h3 {
                color: #A1A09A;
            }

            .wayfinder #room-list-items {
                list-style: none;
                margin: 0;
                padding: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .wayfinder .room-list-item {
                appearance: none;
                border: 1px solid #e3e3e0;
                background: #fdfdfc;
                color: #1b1b18;
                border-radius: 999px;
                padding: 0.35rem 0.75rem;
                font-size: 0.875rem;
                font-weight: 500;
                cursor: pointer;
                transition: background 0.15s, border-color 0.15s, color 0.15s;
            }

            .wayfinder .room-list-item:hover {
                border-color: #e67e22;
                background: #fff7ed;
            }

            .wayfinder .room-list-item.is-active {
                border-color: #e67e22;
                background: #e67e22;
                color: #fff;
            }

            .dark .wayfinder .room-list-item {
                border-color: #3E3E3A;
                background: #161615;
                color: #EDEDEC;
            }

            .dark .wayfinder .room-list-item:hover {
                border-color: #e67e22;
                background: #2a2118;
            }

            .dark .wayfinder .room-list-item.is-active {
                background: #e67e22;
                color: #fff;
            }
        </style>

        <p id="grid-status" style="display: none"></p>
        <p id="file-fallback" style="display: none; font-size: 14px">
            <label for="json-file"><strong>floor-plan.json</strong> (manual fallback): </label>
            <input type="file" id="json-file" accept=".json,application/json" />
            <span style="margin-left: 0.75rem"></span>
            <label for="floor-image-file">Floor image: </label>
            <input type="file" id="floor-image-file" accept=".jpg,.jpeg,image/jpeg,.png,image/png" />
        </p>

        <div id="map-container">
            <img id="floor-img" alt="Floor plan" width="595" height="842" />
            <svg class="overlay" id="floor-plan" viewBox="0 0 595 842" preserveAspectRatio="none">
                <polyline id="path-line" points="" />
                <g id="rooms-layer"></g>
                <circle id="entrance-marker" cx="0" cy="0" r="5" fill="#ff3a3a" stroke="#900" stroke-width="0.6" />
            </svg>
        </div>

        <nav id="room-list" class="room-list" aria-label="Rooms">
            <h3>Rooms</h3>
            <ul id="room-list-items"></ul>
        </nav>

        <script src="https://cdn.jsdelivr.net/npm/pathfinding@0.4.18/visual/lib/pathfinding-browser.min.js"></script>
        <script>
            window.WAYFINDER_ASSETS = @json($mapAssets);
        </script>
        <script src="{{ $scriptUrl }}"></script>
    </div>
@endsection
