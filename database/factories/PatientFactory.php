<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->patient(),
            'date_of_birth' => fake()->date('Y-m-d', '2010-01-01'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}