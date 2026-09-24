<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\View\View;

class QuestBoardController extends Controller
{
    public function __invoke(): View
    {
        $quests = Quest::query()
            ->with(['creator', 'taker'])
            ->latest()
            ->get();

        return view('tools.quest-board', compact('quests'));
    }
}
