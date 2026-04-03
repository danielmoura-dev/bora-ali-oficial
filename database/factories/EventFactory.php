<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $title    = $this->faker->sentence(4, true);
        $startsAt = $this->faker->dateTimeBetween('-1 month', '+3 months');
        $endsAt   = (clone $startsAt)->modify('+' . rand(2, 8) . ' hours');

        return [
            'user_id'             => User::factory(),
            'category_id'         => null,
            'title'               => $title,
            'slug'          => Str::slug($title) . '-' . $this->faker->unique()->randomNumber(4),
            'description'   => $this->faker->paragraphs(3, true),
            'cover_image'   => null,
            'venue_name'    => $this->faker->company(),
            'venue_address' => $this->faker->streetAddress(),
            'city'          => $this->faker->randomElement(['Fortaleza', 'São Paulo', 'Rio de Janeiro', 'Recife', 'Salvador']),
            'state'         => $this->faker->randomElement(['CE', 'SP', 'RJ', 'PE', 'BA']),
            'starts_at'     => $startsAt,
            'ends_at'       => $endsAt,
            'status'              => 'published',
            'is_free'             => $this->faker->boolean(30),
            'absorb_service_fee'  => false,
            'ticket_nomenclature' => 'ingresso',
        ];
    }

    public function current(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->addDays(rand(1, 30)),
            'ends_at'   => now()->addDays(rand(1, 30))->addHours(4),
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subDays(rand(5, 30)),
            'ends_at'   => now()->subDays(rand(1, 4)),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }
}