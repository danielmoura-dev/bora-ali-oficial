<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function refund(User $user, Order $order): bool
    {
        return $user->id === $order->event->user_id;
    }
}