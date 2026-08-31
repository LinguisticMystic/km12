<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable(['name'])]
class Gallery extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Gallery $gallery): void {
            if ($gallery->isDirty('name') || blank($gallery->slug)) {
                $gallery->slug = static::uniqueSlugFor($gallery);
            }
        });

        static::deleting(function (Gallery $gallery): void {
            $gallery->images()->each(function (GalleryImage $image): void {
                $image->delete();
            });
        });
    }

    public static function uniqueSlugFor(Gallery $gallery): string
    {
        $base = Str::slug($gallery->name);

        if ($base === '' || ctype_digit($base)) {
            $base = $base === '' ? 'gallery' : "gallery-{$base}";
        }

        $slug = $base;
        $suffix = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($gallery->exists, fn (Builder $query) => $query->whereKeyNot($gallery->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return HasMany<GalleryImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return HasOne<GalleryImage, $this>
     */
    public function coverImage(): HasOne
    {
        return $this->hasOne(GalleryImage::class)->oldestOfMany('sort_order');
    }
}
