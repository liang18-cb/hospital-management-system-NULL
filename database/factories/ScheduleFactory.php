<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        $start = fake()->time('H:i');
        $end = date('H:i', strtotime($start . ' + ' . rand(1, 4) . ' hours'));

        return [
            'doctor_id' => Doctor::factory(),
            'day_of_week' => fake()->randomElement([
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
                'Minggu'
            ]),
            'start_time' => $start,
            'end_time' => $end,
        ];
    }
}