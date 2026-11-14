<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
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

    public function view(User $user, Brand $brand): bool
    {
        return $brand->isPublished() || $user->hasRole(['super-admin', 'admin', 'editor']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'editor']);
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->hasRole(['super-admin', 'admin', 'editor']);
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Brand $brand): bool
    {
        return $user->isAdmin();
    }
}
