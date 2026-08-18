<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

#[Fillable(['event_id', 'artist_id', 'extra_id', 'sort_order'])]
class EventParticipant extends Model
{
    protected static function booted(): void
    {
        static::saving(function (EventParticipant $participant): void {
            $hasArtist = $participant->artist_id !== null;
            $hasExtra = $participant->extra_id !== null;

            if ($hasArtist === $hasExtra) {
                throw new InvalidArgumentException('Event participant must belong to either an artist or an extra.');
            }
        });

        static::creating(function (EventParticipant $participant): void {
            if ($participant->sort_order !== null) {
                return;
            }

            $participant->sort_order = (int) static::query()
                ->where('event_id', $participant->event_id)
                ->max('sort_order') + 1;
        });
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * @return BelongsTo<Extra, $this>
     */
    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class);
    }

    /**
     * @return HasMany<ScheduleEntry, $this>
     */
    public function scheduleEntries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class)
            ->orderBy('date')
            ->orderBy('starts_at');
    }

    public function displayName(): ?string
    {
        return $this->artist?->name ?? $this->extra?->name;
    }

    public function isExtra(): bool
    {
        return $this->extra_id !== null;
    }
}
