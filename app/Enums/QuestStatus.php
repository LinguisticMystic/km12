<?php

namespace App\Enums;

enum QuestStatus: string
{
    case Available = 'available';
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case Completed = 'completed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Available => __('Available'),
            self::InProgress => __('In Progress'),
            self::Submitted => __('Submitted'),
            self::Completed => __('Completed'),
            self::Rejected => __('Rejected'),
        };
    }

    public function isHiddenFromOtherUsers(): bool
    {
        return $this === self::Submitted || $this === self::Rejected;
    }
}
