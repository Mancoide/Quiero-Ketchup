<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LegalText;
use Illuminate\Auth\Access\HandlesAuthorization;

class LegalTextPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LegalText');
    }

    public function view(AuthUser $authUser, LegalText $legalText): bool
    {
        return $authUser->can('View:LegalText');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LegalText');
    }

    public function update(AuthUser $authUser, LegalText $legalText): bool
    {
        return $authUser->can('Update:LegalText');
    }

    public function delete(AuthUser $authUser, LegalText $legalText): bool
    {
        return $authUser->can('Delete:LegalText');
    }

    public function restore(AuthUser $authUser, LegalText $legalText): bool
    {
        return $authUser->can('Restore:LegalText');
    }

    public function forceDelete(AuthUser $authUser, LegalText $legalText): bool
    {
        return $authUser->can('ForceDelete:LegalText');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LegalText');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LegalText');
    }

    public function replicate(AuthUser $authUser, LegalText $legalText): bool
    {
        return $authUser->can('Replicate:LegalText');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LegalText');
    }

}