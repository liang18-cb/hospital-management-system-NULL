<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    public function run(): void
    {
        if (Schedule::count() === 0) {
            Doctor::all()->each(function ($doctor) {
                Schedule::factory()->count(3)->create([
                    'doctor_id' => $doctor->id,
                ]);
            });
        }
    }
}