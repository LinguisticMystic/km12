<?php

namespace App\Policies;

use App\Enums\QuestStatus;
use App\Models\Quest;
use App\Models\User;

class QuestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, Quest $quest): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Quest $quest): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Quest $quest): bool
    {
        return $user->is_admin;
    }

    public function deleteAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function start(User $user, Quest $quest): bool
    {
        return $quest->status === QuestStatus::Available;
    }

    public function submit(User $user, Quest $quest): bool
    {
        return $quest->taken_by === $user->id
            && in_array($quest->status, [QuestStatus::InProgress, QuestStatus::Rejected], true);
    }

    public function complete(User $user, Quest $quest): bool
    {
        return $this->review($user, $quest);
    }

    public function reject(User $user, Quest $quest): bool
    {
        return $this->review($user, $quest);
    }

    private function review(User $user, Quest $quest): bool
    {
        return $user->is_admin
            && $quest->status === QuestStatus::Submitted;
    }
}
