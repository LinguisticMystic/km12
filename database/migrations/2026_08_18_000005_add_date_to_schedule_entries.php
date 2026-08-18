<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->date('date')->nullable()->after('stage_id');
        });

        foreach (DB::table('schedule_entries')->orderBy('id')->get() as $entry) {
            $date = null;

            if (filled($entry->starts_at)) {
                $date = substr((string) $entry->starts_at, 0, 10);
            } else {
                $eventDate = DB::table('event_participants')
                    ->join('events', 'events.id', '=', 'event_participants.event_id')
                    ->where('event_participants.id', $entry->event_participant_id)
                    ->value('events.date');

                if (filled($eventDate)) {
                    $date = substr((string) $eventDate, 0, 10);
                }
            }

            if ($date === null) {
                continue;
            }

            DB::table('schedule_entries')
                ->where('id', $entry->id)
                ->update(['date' => $date]);
        }

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
            $table->time('starts_at')->nullable()->change();
            $table->time('ends_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dateTime('starts_at')->nullable()->change();
            $table->dateTime('ends_at')->nullable()->change();
        });

        foreach (DB::table('schedule_entries')->orderBy('id')->get() as $entry) {
            $date = $entry->date;

            if (! filled($date)) {
                continue;
            }

            $startsAt = filled($entry->starts_at) ? $date.' '.$entry->starts_at : null;
            $endsAt = filled($entry->ends_at) ? $date.' '.$entry->ends_at : null;

            DB::table('schedule_entries')
                ->where('id', $entry->id)
                ->update([
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ]);
        }

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
