<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $doctor = Doctor::inRandomOrder()->first() ?? Doctor::factory();
        $schedule = Schedule::where('doctor_id', $doctor->id)->inRandomOrder()->first() ?? Schedule::factory(['doctor_id' => $doctor->id]);

        return [
            'patient_id' => Patient::inRandomOrder()->first() ?? Patient::factory(),
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'appointment_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'complaint' => fake('id_ID')->randomElement([
                'Demam tinggi dan batuk berdahak sejak tiga hari yang lalu.',
                'Nyeri dada sebelah kiri berkala setelah beraktivitas berat.',
                'Sakit gigi geraham bawah kanan, gusi mengalami pembengkakan.',
                'Pusing berputar disertai mual dan lemas sejak pagi.',
                'Kontrol rutin berkala untuk cek perkembangan kesehatan anak.'
            ]),
        ];
    }
}