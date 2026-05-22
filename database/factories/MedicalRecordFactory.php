<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'diagnosis' => fake()->sentence(),
            'treatment' => fake()->paragraph(),
        ];
    }
}