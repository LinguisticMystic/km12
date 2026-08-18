<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_participant_id', 'stage_id', 'date', 'starts_at', 'ends_at', 'notes'])]
class ScheduleEntry extends Model
{
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

    public function sortKey(): string
    {
        return sprintf(
            '%s-%s',
            $this->date?->toDateString() ?? '',
            $this->starts_at?->format('H:i:s') ?? '',
        );
    }
}
