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
            'artistParticipants.artist.participantType',
            'artistParticipants.artist.genres',
            'artistParticipants.scheduleEntries' => fn ($query) => $query->orderBy('date')->orderBy('starts_at'),
            'artistParticipants.scheduleEntries.stage',
            'extraParticipants.extra.extraType',
            'extraParticipants.scheduleEntries' => fn ($query) => $query->orderBy('date')->orderBy('starts_at'),
            'extraParticipants.scheduleEntries.stage',
        ]);

        $scheduleByStage = $event->scheduleGroupedByStage();

        return view('events.show', compact('event', 'scheduleByStage'));
    }
}
