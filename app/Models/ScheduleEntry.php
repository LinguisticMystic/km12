<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable(['event_participant_id', 'stage_id', 'date', 'starts_at', 'ends_at', 'notes', 'notes_en'])]
class ScheduleEntry extends Model
{
    /**
     * Sets that start before this hour belong to the previous night's lineup.
     */
    public const NIGHT_ENDS_AT_HOUR = 6;

    protected static function booted(): void
    {
        static::saving(function (ScheduleEntry $entry): void {
            if ($entry->starts_at === null) {
                $entry->ends_at = null;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<EventParticipant, $this>
     */
    public function eventParticipant(): BelongsTo
    {
        return $this->belongsTo(EventParticipant::class);
    }

    /**
     * @return BelongsTo<Stage, $this>
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function isAllDay(): bool
    {
        return $this->starts_at === null;
    }

    /**
     * After-midnight sets (00:00–05:59) continue the previous evening.
     */
    public function belongsToPreviousNight(): bool
    {
        return $this->starts_at !== null
            && $this->starts_at->hour < self::NIGHT_ENDS_AT_HOUR;
    }

    public function programDate(): ?Carbon
    {
        if ($this->date === null) {
            return null;
        }

        return $this->belongsToPreviousNight()
            ? $this->date->copy()->subDay()
            : $this->date->copy();
    }

    public function sortKey(): string
    {
        $time = '';

        if ($this->starts_at !== null) {
            $hour = $this->starts_at->hour;

            if ($this->belongsToPreviousNight()) {
                $hour += 24;
            }

            $time = sprintf('%02d:%s', $hour, $this->starts_at->format('i:s'));
        }

        return sprintf(
            '%s-%s',
            $this->programDate()?->toDateString() ?? '',
            $time,
        );
    }

    /**
     * When this row finishes, including overnight sets that end after midnight.
     */
    public function finishesAt(): ?Carbon
    {
        if ($this->date === null) {
            return null;
        }

        if ($this->isAllDay()) {
            return $this->date->copy()->endOfDay();
        }

        $start = $this->date->copy()->setTimeFrom($this->starts_at);

        if ($this->ends_at === null) {
            return $start;
        }

        $end = $this->date->copy()->setTimeFrom($this->ends_at);

        if ($end->lt($start)) {
            $end->addDay();
        }

        return $end;
    }

    public function isUpcoming(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->finishesAt()?->gte($at) ?? false;
    }

    public function localizedNotes(): ?string
    {
        $notes = localized_text($this->notes, $this->notes_en);

        return $notes === '' ? null : $notes;
    }
}
