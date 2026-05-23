<?php

namespace Database\Seeders;

use App\Models\MedicalRecord;
use Illuminate\Database\Seeder;

class MedicalRecordsSeeder extends Seeder
{
    public function run(): void
    {
        if (MedicalRecord::count() === 0) {
            MedicalRecord::factory()->count(50)->create();
        }
    }
}