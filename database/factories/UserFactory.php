<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            // Tambahkan role default agar factory tidak error jika dipanggil langsung
            'role' => fake()->randomElement(['admin', 'doctor', 'patient']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State khusus untuk Admin
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * State khusus untuk Dokter
     */
    public function doctor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'doctor',
        ]);
    }

    /**
     * State khusus untuk Pasien
     */
    public function patient(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'patient',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}