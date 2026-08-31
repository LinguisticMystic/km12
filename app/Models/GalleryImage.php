<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['gallery_id', 'path', 'annotation', 'sort_order'])]
class GalleryImage extends Model
{
    protected static function booted(): void
    {
        static::deleting(function (GalleryImage $image): void {
            if (filled($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }

    /**
     * @return BelongsTo<Gallery, $this>
     */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function url(): ?string
    {
        if (! filled($this->path)) {
            return null;
        }

        return url(Storage::disk('public')->url($this->path));
    }
}
