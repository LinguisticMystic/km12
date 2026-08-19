<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\ScheduleEntry;
use Tests\TestCase;

class ScheduleEntryTest extends TestCase
{
    public function test_after_midnight_sets_belong_to_the_previous_program_date(): void
    {
        $late = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => '01:00',
        ]);

        $this->assertTrue($late->belongsToPreviousNight());
        $this->assertSame('2026-08-28', $late->programDate()?->toDateString());
    }

    public function test_evening_six_am_and_all_day_sets_stay_on_their_calendar_date(): void
    {
        $evening = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => '19:00',
        ]);
        $sixAm = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => '06:00',
        ]);
        $allDay = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => null,
        ]);

        $this->assertFalse($evening->belongsToPreviousNight());
        $this->assertFalse($sixAm->belongsToPreviousNight());
        $this->assertFalse($allDay->belongsToPreviousNight());
        $this->assertSame('2026-08-29', $evening->programDate()?->toDateString());
        $this->assertSame('2026-08-29', $sixAm->programDate()?->toDateString());
        $this->assertSame('2026-08-29', $allDay->programDate()?->toDateString());
    }

    public function test_sort_key_orders_a_night_from_evening_into_after_midnight(): void
    {
        $fridayEvening = new ScheduleEntry([
            'date' => '2026-08-28',
            'starts_at' => '22:00',
        ]);
        $saturdayLate = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => '01:00',
        ]);
        $saturdayEvening = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => '19:00',
        ]);

        $this->assertTrue($fridayEvening->sortKey() < $saturdayLate->sortKey());
        $this->assertTrue($saturdayLate->sortKey() < $saturdayEvening->sortKey());
    }

    public function test_event_groups_after_midnight_sets_under_the_previous_night(): void
    {
        $event = new Event;
        $participant = new EventParticipant;
        $fridayEvening = new ScheduleEntry([
            'date' => '2026-08-28',
            'starts_at' => '22:00',
        ]);
        $saturdayLate = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => '01:00',
        ]);
        $saturdayEvening = new ScheduleEntry([
            'date' => '2026-08-29',
            'starts_at' => '19:00',
        ]);

        $participant->setRelation('scheduleEntries', collect([
            $saturdayEvening,
            $saturdayLate,
            $fridayEvening,
        ]));
        $event->setRelation('participants', collect([$participant]));

        $grouped = $event->scheduleGroupedByDay();

        $this->assertSame(['2026-08-28', '2026-08-29'], $grouped->keys()->all());
        $this->assertSame(
            ['22:00', '01:00'],
            $grouped['2026-08-28']->map(fn (ScheduleEntry $entry) => $entry->starts_at->format('H:i'))->values()->all(),
        );
        $this->assertSame(
            ['19:00'],
            $grouped['2026-08-29']->map(fn (ScheduleEntry $entry) => $entry->starts_at->format('H:i'))->values()->all(),
        );
    }
}
