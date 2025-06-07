<?php

namespace Modules\Payments\Policies;

use App\Models\User;
use Modules\Payments\Models\Transfer;

class TransferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('transfers.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transfer $Transfer): bool
    {
        return $user->can('transfer.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('transfers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transfer $Transfer): bool
    {
        return $user->can('transfers.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transfer $Transfer): bool
    {
        return $user->can('transfers.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transfer $Transfer): bool
    {
        return $user->can('transfers.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transfer $Transfer): bool
    {
        return $user->can('transfers.forceDelete');
    }
}
