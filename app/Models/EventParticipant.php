<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['event_id', 'participant_type_id', 'name', 'bio', 'image_path', 'sort_order'])]
class EventParticipant extends Model
{
    protected static function booted(): void
    {
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
     * @return BelongsTo<ParticipantType, $this>
     */
    public function participantType(): BelongsTo
    {
        return $this->belongsTo(ParticipantType::class);
    }

    /**
     * @return HasMany<ScheduleEntry, $this>
     */
    public function scheduleEntries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class)->orderBy('starts_at');
    }

    public function imageUrl(): ?string
    {
        if (! filled($this->image_path)) {
            return null;
        }

        return url(Storage::disk('public')->url($this->image_path));
    }
}
