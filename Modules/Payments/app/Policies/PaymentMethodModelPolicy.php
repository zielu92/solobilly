<?php

namespace Modules\Payments\Policies;

use App\Models\User;
use Modules\Payments\Models\PaymentMethodModel;

class PaymentMethodModelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('paymentMethods.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentMethodModel $paymentMethodModel): bool
    {
        return $user->can('paymentMethod.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('paymentMethods.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentMethodModel $paymentMethodModel): bool
    {
        return $user->can('paymentMethods.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentMethodModel $paymentMethodModel): bool
    {
        return $user->can('paymentMethods.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaymentMethodModel $paymentMethodModel): bool
    {
        return $user->can('paymentMethods.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PaymentMethodModel $paymentMethodModel): bool
    {
        return $user->can('paymentMethods.forceDelete');
    }
}
