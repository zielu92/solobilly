<?php

namespace App\Policies;

use App\Models\Tax;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaxPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('taxes.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tax $taxes): bool
    {
        return $user->can('tax.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('invoices.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tax $taxes): bool
    {
        return $user->can('invoices.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tax $taxes): bool
    {
        return $user->can('invoices.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tax $taxes): bool
    {
        return $user->can('invoices.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tax $taxes): bool
    {
        return $user->can('invoices.forceDelete');
    }
}
