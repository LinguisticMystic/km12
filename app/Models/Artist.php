<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['participant_type_id', 'name', 'bio', 'bio_en', 'image_path'])]
class Artist extends Model
{
    /**
     * @return BelongsTo<ParticipantType, $this>
     */
    public function participantType(): BelongsTo
    {
        return $this->belongsTo(ParticipantType::class);
    }

    /**
     * @return BelongsToMany<Genre, $this>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->orderBy('name');
    }

    /**
     * @return HasMany<EventParticipant, $this>
     */
    public function eventParticipations(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    /**
     * @return BelongsToMany<Event, $this>
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withPivot('id', 'sort_order')
            ->withTimestamps()
            ->orderBy('date');
    }

    public function localizedBio(): ?string
    {
        $bio = localized_text($this->bio, $this->bio_en);

        return $bio === '' ? null : $bio;
    }

    public function imageUrl(): ?string
    {
        if (! filled($this->image_path)) {
            return null;
        }

        return url(Storage::disk('public')->url($this->image_path));
    }
}
