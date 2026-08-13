<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'sort_order'])]
class ParticipantType extends Model
{
    protected static function booted(): void
    {
        static::creating(function (ParticipantType $type): void {
            $type->sort_order ??= (int) static::query()->max('sort_order') + 1;
        });
    }

    /**
     * @return HasMany<Artist, $this>
     */
    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class);
    }
}
