<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'password'          => bcrypt('Senha@1234'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'onboarding_step'   => 4,
            'profile_type'      => 'cpf',
            'document_number'   => '52998224725',
            'birth_date'        => '1990-01-01',
        ];
    }
}