<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuyerPolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('buyers.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Buyer $buyer): bool
    {
        return $user->can('buyer.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('buyers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Buyer $buyer): bool
    {
        return $user->can('buyers.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Buyer $buyer): bool
    {
        return $user->can('buyers.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Buyer $buyer): bool
    {
        return $user->can('buyers.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Buyer $buyer): bool
    {
        return $user->can('buyers.forceDelete');
    }
}
