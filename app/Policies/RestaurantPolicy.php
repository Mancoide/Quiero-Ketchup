<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Restaurant;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestaurantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Restaurant');
    }

    public function view(AuthUser $authUser, Restaurant $restaurant): bool
    {
        return $authUser->can('View:Restaurant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Restaurant');
    }

    public function update(AuthUser $authUser, Restaurant $restaurant): bool
    {
        return $authUser->can('Update:Restaurant');
    }

    public function delete(AuthUser $authUser, Restaurant $restaurant): bool
    {
        return $authUser->can('Delete:Restaurant');
    }

    public function restore(AuthUser $authUser, Restaurant $restaurant): bool
    {
        return $authUser->can('Restore:Restaurant');
    }

    public function forceDelete(AuthUser $authUser, Restaurant $restaurant): bool
    {
        return $authUser->can('ForceDelete:Restaurant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Restaurant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Restaurant');
    }

    public function replicate(AuthUser $authUser, Restaurant $restaurant): bool
    {
        return $authUser->can('Replicate:Restaurant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Restaurant');
    }

}