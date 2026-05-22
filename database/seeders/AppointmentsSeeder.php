<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        if (Appointment::count() === 0) {
            Appointment::factory()->count(200)->create();
        }
    }
}