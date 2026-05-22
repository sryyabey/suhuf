<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\QuranWord;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuranWordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QuranWord');
    }

    public function view(AuthUser $authUser, QuranWord $quranWord): bool
    {
        return $authUser->can('View:QuranWord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QuranWord');
    }

    public function update(AuthUser $authUser, QuranWord $quranWord): bool
    {
        return $authUser->can('Update:QuranWord');
    }

    public function delete(AuthUser $authUser, QuranWord $quranWord): bool
    {
        return $authUser->can('Delete:QuranWord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QuranWord');
    }

    public function restore(AuthUser $authUser, QuranWord $quranWord): bool
    {
        return $authUser->can('Restore:QuranWord');
    }

    public function forceDelete(AuthUser $authUser, QuranWord $quranWord): bool
    {
        return $authUser->can('ForceDelete:QuranWord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QuranWord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QuranWord');
    }

    public function replicate(AuthUser $authUser, QuranWord $quranWord): bool
    {
        return $authUser->can('Replicate:QuranWord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QuranWord');
    }

}