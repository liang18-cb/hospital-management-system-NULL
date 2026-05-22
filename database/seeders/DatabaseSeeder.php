<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            DoctorsSeeder::class,
            PatientsSeeder::class,
            SchedulesSeeder::class,
            AppointmentsSeeder::class,
        ]);
    }
}