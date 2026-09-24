<?php

namespace App\Http\Controllers;

use App\Enums\QuestStatus;
use App\Models\Quest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestController extends Controller
{
    public function start(Request $request, Quest $quest): RedirectResponse
    {
        Gate::authorize('start', $quest);

        $started = Quest::query()
            ->whereKey($quest->id)
            ->where('status', QuestStatus::Available)
            ->update([
                'status' => QuestStatus::InProgress,
                'taken_by' => $request->user()->id,
            ]);

        abort_unless($started === 1, 403);

        return back();
    }

    public function submit(Request $request, Quest $quest): RedirectResponse
    {
        Gate::authorize('submit', $quest);

        $submitted = Quest::query()
            ->whereKey($quest->id)
            ->where('taken_by', $request->user()->id)
            ->whereIn('status', [QuestStatus::InProgress, QuestStatus::Rejected])
            ->update([
                'status' => QuestStatus::Submitted,
            ]);

        abort_unless($submitted === 1, 403);

        return back();
    }
}
