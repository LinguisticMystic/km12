<?php

namespace App\Policies;

use App\Models\ExtraType;
use App\Models\User;

class ExtraTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, ExtraType $extraType): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, ExtraType $extraType): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, ExtraType $extraType): bool
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
