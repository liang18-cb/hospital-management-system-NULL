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
        $appointment = Appointment::where('status', 'completed')->inRandomOrder()->first() ?? Appointment::factory(['status' => 'completed']);

        return [
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'diagnosis' => fake()->sentence(),
            'prescription' => fake()->paragraph(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}