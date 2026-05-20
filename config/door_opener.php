<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Arduino API token
    |--------------------------------------------------------------------------
    |
    | Shared secret sent by the ESP32 as "Authorization: Bearer {token}"
    | when polling for commands. Generate a long random string for production.
    |
    */

    'arduino_token' => env('DOOR_OPENER_ARDUINO_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Command queue
    |--------------------------------------------------------------------------
    |
    | How long a queued open-door / open-gate command stays available for the
    | Arduino to pick up before it expires.
    |
    */

    'command_ttl_seconds' => (int) env('DOOR_OPENER_COMMAND_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Door relay duration (seconds)
    |--------------------------------------------------------------------------
    |
    | How long the door relay stays active. The Arduino uses this value from
    | the poll response so web and firmware stay in sync.
    |
    */

    'door_open_seconds' => (int) env('DOOR_OPENER_DOOR_SECONDS', 10),

];
