<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'editor', 'author']);
    }

    public function view(User $user, Device $device): bool
    {
        return $device->isPublished() || $user->hasRole(['super-admin', 'admin', 'editor', 'author']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'editor', 'author']);
    }

    public function update(User $user, Device $device): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'editor', 'author']);
    }

    public function publish(User $user, Device $device): bool
    {
        return $user->isEditor();
    }

    public function delete(User $user, Device $device): bool
    {
        if ($user->isEditor()) {
            return true;
        }

        if ($user->isAuthor() && $device->status === 'draft') {
            return true;
        }

        return false;
    }

    public function restore(User $user, Device $device): bool
    {
        return $user->isAdmin();
    }
}
