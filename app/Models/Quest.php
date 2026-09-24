<?php

namespace App\Models;

use App\Enums\QuestStatus;
use Database\Factories\QuestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'description', 'experience_points', 'created_by', 'status', 'taken_by'])]
class Quest extends Model
{
    /** @use HasFactory<QuestFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Quest $quest): void {
            if (blank($quest->created_by) && auth()->check()) {
                $quest->created_by = auth()->id();
            }

            $quest->status ??= QuestStatus::Available;
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'experience_points' => 'integer',
            'status' => QuestStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function taker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    public function statusVisibleTo(?User $user): QuestStatus
    {
        $status = $this->status ?? QuestStatus::Available;

        if ($status->isHiddenFromOtherUsers() && $user?->id !== $this->taken_by) {
            return QuestStatus::InProgress;
        }

        return $status;
    }

    public function complete(): void
    {
        $this->forceFill([
            'status' => QuestStatus::Completed,
        ])->save();
    }

    public function reject(): void
    {
        $this->forceFill([
            'status' => QuestStatus::Rejected,
        ])->save();
    }
}
