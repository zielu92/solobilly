<?php

namespace App\Policies;

use App\Models\Cost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('costs.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cost $cost): bool
    {
        return $user->can('cost.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('costs.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cost $cost): bool
    {
        return $user->can('costs.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cost $cost): bool
    {
        return $user->can('costs.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cost $cost): bool
    {
        return $user->can('costs.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cost $cost): bool
    {
        return $user->can('costs.forceDelete');
    }
}
