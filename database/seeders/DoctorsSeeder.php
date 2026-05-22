<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorsSeeder extends Seeder
{
    public function run(): void
    {
        if (Doctor::count() === 0) {
            Doctor::factory()->count(10)->create();
        }
    }
}