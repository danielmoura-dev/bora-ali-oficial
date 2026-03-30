<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal     = $this->faker->randomElement([2000, 5000, 10000]);
        $platformFee  = 100;

        return [
            'user_id'      => User::factory(),
            'event_id'     => Event::factory(),
            'reference'    => Order::generateReference(),
            'subtotal'     => $subtotal,
            'platform_fee' => $platformFee,
            'total'        => $subtotal + $platformFee,
            'status'       => 'pending',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status'         => 'paid',
            'payment_method' => 'credit_card',
            'payment_id'     => 'mock_' . fake()->uuid(),
        ]);
    }
}