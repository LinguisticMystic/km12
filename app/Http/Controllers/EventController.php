<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->orderedForListing()
            ->get();

        return view('events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        return view('events.show', compact('event'));
    }
}
