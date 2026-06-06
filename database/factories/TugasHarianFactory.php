<?php

namespace Database\Factories;

use App\Models\Kandang;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TugasHarian>
 */
class TugasHarianFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul'       => fake()->sentence(4),
            'deskripsi'   => fake()->optional(0.6)->paragraph(),
            'kandang_id'  => Kandang::factory(),
            'user_id'     => null,
            'assigned_by' => null,
            'tanggal'     => today()->toDateString(),
            'tipe'        => fake()->randomElement(['rutin', 'kondisional']),
            'prioritas'   => fake()->randomElement(['rendah', 'sedang', 'tinggi']),
            'status'      => 'belum',
            'waktu_mulai' => null,
            'waktu_selesai' => null,
            'catatan_penyelesaian' => null,
        ];
    }

    public function selesai(): static
    {
        return $this->state([
            'status'        => 'selesai',
            'waktu_mulai'   => '08:00',
            'waktu_selesai' => '09:00',
        ]);
    }

    public function dalamProses(): static
    {
        return $this->state([
            'status'      => 'dalam_proses',
            'waktu_mulai' => '08:00',
        ]);
    }

    public function prioritasTinggi(): static
    {
        return $this->state(['prioritas' => 'tinggi']);
    }

    public function rutin(): static
    {
        return $this->state(['tipe' => 'rutin']);
    }

    public function kondisional(): static
    {
        return $this->state(['tipe' => 'kondisional']);
    }
}
