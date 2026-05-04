<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kandang>
 */
class KandangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kandang' => 'Kandang ' . fake()->unique()->bothify('??-###'),
            'tipe'         => fake()->randomElement(['utama', 'isolasi', 'kawin', 'persalinan']),
            'kapasitas'    => fake()->numberBetween(10, 100),
        ];
    }

    public function utama(): static
    {
        return $this->state(['tipe' => 'utama']);
    }

    public function isolasi(): static
    {
        return $this->state(['tipe' => 'isolasi']);
    }
}
