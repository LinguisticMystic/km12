<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'sort_order'])]
class Stage extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Stage $stage): void {
            $stage->sort_order ??= (int) static::query()->max('sort_order') + 1;
        });
    }

    /**
     * @return HasMany<ScheduleEntry, $this>
     */
    public function scheduleEntries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class);
    }
}
