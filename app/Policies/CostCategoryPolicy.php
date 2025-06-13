<?php

namespace App\Policies;

use App\Models\CostCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CostCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('costCategories.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CostCategory $costCategory): bool
    {
        return $user->can('costCategory.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('costCategories.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CostCategory $costCategory): bool
    {
        return $user->can('costCategories.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CostCategory $costCategory): bool
    {
        return $user->can('costCategories.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CostCategory $costCategory): bool
    {
        return $user->can('costCategories.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CostCategory $costCategory): bool
    {
        return $user->can('costCategories.forceDelete');
    }
}
