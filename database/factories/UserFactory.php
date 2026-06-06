<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'role'              => 'admin',
            'status'            => 'aktif',
            'nomor_hp'          => fake()->numerify('08##########'),
            'remember_token'    => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(['role' => 'super_admin']);
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function kepalaKandang(): static
    {
        return $this->state(['role' => 'kepala_kandang']);
    }

    public function pengurusKandang(): static
    {
        return $this->state(['role' => 'pengurus_kandang']);
    }

    public function nonaktif(): static
    {
        return $this->state(['status' => 'nonaktif']);
    }
}
