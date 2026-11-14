<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
        return $user->hasPermission('posts.view') || $user->isAuthor();
    }

    public function view(User $user, Post $post): bool
    {
        if ($post->isPublished()) {
            return true;
        }

        if ($user->isEditor()) {
            return true;
        }

        return $post->author_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('posts.create') || $user->isAuthor();
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->isEditor()) {
            return true;
        }

        if ($user->isAuthor() && $post->author_id === $user->id) {
            return true;
        }

        return false;
    }

    public function publish(User $user, Post $post): bool
    {
        return $user->hasPermission('posts.publish') || $user->isEditor();
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->isEditor()) {
            return true;
        }

        // Author can only delete their own draft posts
        if ($user->isAuthor() && $post->author_id === $user->id && $post->status === 'draft') {
            return true;
        }

        return false;
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
