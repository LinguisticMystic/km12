<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'date', 'description', 'ticket_url', 'poster_path'])]
class Event extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function posterUrl(): ?string
    {
        if (! filled($this->poster_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->poster_path);
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
