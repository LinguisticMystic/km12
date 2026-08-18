<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'sort_order'])]
class ExtraType extends Model
{
    protected static function booted(): void
    {
        static::creating(function (ExtraType $type): void {
            $type->sort_order ??= (int) static::query()->max('sort_order') + 1;
        });
    }

    /**
     * @return HasMany<Extra, $this>
     */
    public function extras(): HasMany
    {
        return $this->hasMany(Extra::class);
    }
}
