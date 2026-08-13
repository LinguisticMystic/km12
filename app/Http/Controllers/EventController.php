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
        $event->load([
            'participants.artist.participantType',
            'participants.artist.genres',
            'participants.scheduleEntries' => fn ($query) => $query->orderBy('starts_at'),
            'participants.scheduleEntries.stage',
        ]);

        $scheduleByStage = $event->scheduleGroupedByStage();

        return view('events.show', compact('event', 'scheduleByStage'));
    }
}
