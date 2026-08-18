<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['extra_type_id', 'name', 'bio', 'bio_en', 'instagram_url', 'website_url', 'image_path'])]
class Extra extends Model
{
    /**
     * @return BelongsTo<ExtraType, $this>
     */
    public function extraType(): BelongsTo
    {
        return $this->belongsTo(ExtraType::class);
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
