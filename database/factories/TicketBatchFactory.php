<?php

namespace Database\Factories;

use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketBatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_type_id' => TicketType::factory(),
            'name'           => $this->faker->randomElement(['1º Lote', '2º Lote', '3º Lote']),
            'quantity'       => $this->faker->numberBetween(50, 500),
            'quantity_sold'  => 0,
            'price'          => $this->faker->randomElement([2000, 3000, 5000, 8000, 10000]),
            'starts_at'      => now()->subDay(),
            'ends_at'        => now()->addDays(30),
            'is_active'      => true,
        ];
    }

    public function soldOut(): static
    {
        return $this->state(fn (array $attrs) => [
            'quantity'      => 100,
            'quantity_sold' => 100,
        ]);
    }
}