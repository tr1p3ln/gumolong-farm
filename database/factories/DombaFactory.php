<?php

namespace Database\Factories;

use App\Models\Kandang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Domba>
 */
class DombaFactory extends Factory
{
    public function definition(): array
    {
        $jk     = fake()->randomElement(['jantan', 'betina']);
        $prefix = $jk === 'jantan' ? 'J' : 'B';

        return [
            'ear_tag_id'    => $prefix . '-' . fake()->unique()->numerify('###'),
            'nama'          => fake()->optional(0.7)->firstName(),
            'jenis_kelamin' => $jk,
            'ras'           => fake()->randomElement(['Merino', 'Dorper', 'Suffolk', 'Garut', 'Ekor Tipis']),
            'tanggal_lahir' => fake()->optional(0.8)->dateTimeBetween('-5 years', '-1 month'),
            'kategori'      => fake()->randomElement(['cempe', 'dara', 'indukan', 'pejantan']),
            'status'        => 'aktif',
            'asal'          => fake()->randomElement(['lahir_di_kandang', 'dari_luar']),
            'catatan'       => fake()->optional(0.3)->sentence(),
            'kandang_id'    => Kandang::factory(),
        ];
    }

    public function jantan(): static
    {
        return $this->state(fn(array $attr) => [
            'jenis_kelamin' => 'jantan',
            'ear_tag_id'    => 'J-' . fake()->unique()->numerify('###'),
            'kategori'      => 'pejantan',
        ]);
    }

    public function betina(): static
    {
        return $this->state(fn(array $attr) => [
            'jenis_kelamin' => 'betina',
            'ear_tag_id'    => 'B-' . fake()->unique()->numerify('###'),
            'kategori'      => 'indukan',
        ]);
    }

    public function aktif(): static
    {
        return $this->state(['status' => 'aktif']);
    }

    public function mati(): static
    {
        return $this->state(['status' => 'mati']);
    }

    public function karantina(): static
    {
        return $this->state(['status' => 'karantina']);
    }
}
