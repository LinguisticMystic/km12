<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'date', 'description', 'description_en', 'ticket_url', 'poster_path'])]
class Event extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Event $event): void {
            if ($event->isDirty('name') || blank($event->slug)) {
                $event->slug = static::uniqueSlugFor($event);
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public static function uniqueSlugFor(Event $event): string
    {
        $base = Str::slug($event->name);

        if ($base === '' || ctype_digit($base)) {
            $base = $base === '' ? 'event' : "event-{$base}";
        }

        $slug = $base;
        $suffix = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($event->exists, fn (Builder $query) => $query->whereKeyNot($event->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return HasMany<EventParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class)
            ->orderBy('sort_order');
    }

    /**
     * @return HasMany<EventParticipant, $this>
     */
    public function artistParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class)
            ->whereNotNull('artist_id')
            ->orderBy('sort_order')
            ->orderBy(
                Artist::query()
                    ->select('name')
                    ->whereColumn('artists.id', 'event_participants.artist_id'),
            );
    }

    /**
     * @return HasMany<EventParticipant, $this>
     */
    public function extraParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class)
            ->whereNotNull('extra_id')
            ->orderBy('sort_order')
            ->orderBy(
                Extra::query()
                    ->select('name')
                    ->whereColumn('extras.id', 'event_participants.extra_id'),
            );
    }

    /**
     * @return HasManyThrough<ScheduleEntry, EventParticipant, $this>
     */
    public function scheduleEntries(): HasManyThrough
    {
        return $this->hasManyThrough(
            ScheduleEntry::class,
            EventParticipant::class,
            'event_id',
            'event_participant_id',
        )->orderBy('schedule_entries.date')->orderBy('schedule_entries.starts_at');
    }

    /**
     * Schedule entries grouped by stage (stage sort_order, then chronological).
     *
     * @return Collection<int|string, Collection<int, ScheduleEntry>>
     */
    public function scheduleGroupedByStage(): Collection
    {
        if ($this->relationLoaded('participants')) {
            $participants = $this->participants;
        } elseif ($this->relationLoaded('artistParticipants') || $this->relationLoaded('extraParticipants')) {
            $participants = collect()
                ->concat($this->relationLoaded('artistParticipants') ? $this->artistParticipants : [])
                ->concat($this->relationLoaded('extraParticipants') ? $this->extraParticipants : []);
        } else {
            $participants = null;
        }

        if ($participants !== null) {
            $entries = $participants->flatMap(function (EventParticipant $participant) {
                return $participant->scheduleEntries->map(function (ScheduleEntry $entry) use ($participant) {
                    if (! $entry->relationLoaded('eventParticipant')) {
                        $entry->setRelation('eventParticipant', $participant);
                    }

                    return $entry;
                });
            });
        } else {
            $entries = $this->scheduleEntries()->with([
                'stage',
                'eventParticipant.artist',
                'eventParticipant.extra',
            ])->get();
        }

        return $entries
            ->sortBy(fn (ScheduleEntry $entry): string => $entry->sortKey())
            ->groupBy(fn (ScheduleEntry $entry): int => (int) $entry->stage_id)
            ->sortBy(function (Collection $group): string {
                /** @var ScheduleEntry|null $first */
                $first = $group->first();
                $stage = $first?->stage;

                return sprintf('%05d-%s', $stage?->sort_order ?? 99999, $stage?->name ?? '');
            });
    }

    public function localizedDescription(): string
    {
        return localized_text($this->description, $this->description_en);
    }

    public function posterUrl(): ?string
    {
        if (! filled($this->poster_path)) {
            return null;
        }

        // url() uses the current request host (or APP_URL), so /storage stays origin-correct.
        return url(Storage::disk('public')->url($this->poster_path));
    }

    /**
     * Upcoming events first (soonest first), then past events.
     *
     * @param  Builder<Event>  $query
     * @return Builder<Event>
     */
    public function scopeOrderedForListing(Builder $query): Builder
    {
        return $query
            ->orderByRaw('(date < ?) asc', [now()])
            ->orderBy('date');
    }
}
