<?php

namespace App\Http\Controllers;

use App\Services\DoorOpenerCommandQueue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoorOpenerController extends Controller
{
    public function __construct(
        private DoorOpenerCommandQueue $queue,
    ) {}

    public function openDoor(): JsonResponse
    {
        $this->queue->queue('door');

        return response()->json([
            'message' => 'Door open requested.',
        ]);
    }

    public function openGate(): JsonResponse
    {
        $this->queue->queue('gate');

        return response()->json([
            'message' => 'Gate open requested.',
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        if (! $this->arduinoAuthorized($request)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $command = $this->queue->pull();

        return response()->json([
            'command' => $command,
            'door_open_seconds' => config('door_opener.door_open_seconds'),
        ]);
    }

    public function doorbell(Request $request): JsonResponse
    {
        if (! $this->arduinoAuthorized($request)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        // Reserved for future notifications (email, push, etc.)
        return response()->json(['message' => 'Doorbell received.']);
    }

    private function arduinoAuthorized(Request $request): bool
    {
        $token = config('door_opener.arduino_token');

        if (! $token) {
            return false;
        }

        $bearer = $request->bearerToken();
        $queryToken = $request->query('token');

        return ($bearer !== null && hash_equals($token, $bearer))
            || ($queryToken !== null && hash_equals($token, $queryToken));
    }
}
