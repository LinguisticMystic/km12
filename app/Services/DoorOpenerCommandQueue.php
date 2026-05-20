<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DoorOpenerCommandQueue
{
    private const CACHE_KEY = 'door_opener:pending_command';

    public function queue(string $command): void
    {
        Cache::put(self::CACHE_KEY, $command, config('door_opener.command_ttl_seconds'));
    }

    public function pull(): ?string
    {
        $command = Cache::get(self::CACHE_KEY);

        if ($command !== null) {
            Cache::forget(self::CACHE_KEY);
        }

        return $command;
    }

    public function peek(): ?string
    {
        return Cache::get(self::CACHE_KEY);
    }
}
