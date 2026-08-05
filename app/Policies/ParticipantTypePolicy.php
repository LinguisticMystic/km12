<?php

namespace App\Policies;

use App\Models\ParticipantType;
use App\Models\User;

class ParticipantTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, ParticipantType $participantType): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, ParticipantType $participantType): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, ParticipantType $participantType): bool
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
