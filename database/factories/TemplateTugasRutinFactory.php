<?php

namespace Database\Factories;

use App\Models\Kandang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemplateTugasRutin>
 */
class TemplateTugasRutinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul'         => fake()->sentence(4),
            'deskripsi'     => fake()->optional(0.5)->paragraph(),
            'kandang_id'    => Kandang::factory(),
            'user_id'       => null,
            'waktu_default' => fake()->optional(0.7)->time('H:i'),
            'prioritas'     => fake()->randomElement(['rendah', 'sedang', 'tinggi']),
            'is_active'     => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
