<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id'     => Event::factory(),
            'name'         => $this->faker->randomElement(['Inteira', 'Meia-entrada', 'VIP', 'Camarote']),
            'description'  => $this->faker->optional()->sentence(),
            'is_half_price'=> false,
            'sort_order'   => 0,
        ];
    }
}