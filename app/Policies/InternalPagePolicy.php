<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InternalPage;
use Illuminate\Auth\Access\HandlesAuthorization;

class InternalPagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InternalPage');
    }

    public function view(AuthUser $authUser, InternalPage $internalPage): bool
    {
        return $authUser->can('View:InternalPage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InternalPage');
    }

    public function update(AuthUser $authUser, InternalPage $internalPage): bool
    {
        return $authUser->can('Update:InternalPage');
    }

    public function delete(AuthUser $authUser, InternalPage $internalPage): bool
    {
        return $authUser->can('Delete:InternalPage');
    }

    public function restore(AuthUser $authUser, InternalPage $internalPage): bool
    {
        return $authUser->can('Restore:InternalPage');
    }

    public function forceDelete(AuthUser $authUser, InternalPage $internalPage): bool
    {
        return $authUser->can('ForceDelete:InternalPage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InternalPage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InternalPage');
    }

    public function replicate(AuthUser $authUser, InternalPage $internalPage): bool
    {
        return $authUser->can('Replicate:InternalPage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InternalPage');
    }

}