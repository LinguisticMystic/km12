<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'sort_order'])]
class Genre extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Genre $genre): void {
            $genre->sort_order ??= (int) static::query()->max('sort_order') + 1;
        });
    }

    /**
     * @return BelongsToMany<Artist, $this>
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }
}
