<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->isAdmin() || $payment->booking?->id_user === $user->id;
    }

    public function verify(User $user): bool
    {
        return $user->isAdmin();
    }
}
