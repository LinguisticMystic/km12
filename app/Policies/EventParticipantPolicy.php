<?php

namespace App\Policies;

use App\Models\EventParticipant;
use App\Models\User;

class EventParticipantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, EventParticipant $eventParticipant): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, EventParticipant $eventParticipant): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, EventParticipant $eventParticipant): bool
    {
        return $user->is_admin;
    }

    public function deleteAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function reorder(User $user): bool
    {
        return $user->is_admin;
    }
}
