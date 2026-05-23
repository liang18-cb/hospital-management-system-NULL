<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\File;
use App\Models\MedicalRecord;
use App\Models\User;

class FilesSeeder extends Seeder
{
    public function run(): void
    {
        $medicalRecords = MedicalRecord::take(5)->get();
        $doctorUser = User::where('role', 'doctor')->first();
        $adminUser = User::where('role', 'admin')->first();

        $uploaderId = $doctorUser?->id ?? $adminUser?->id ?? 1;

        if ($medicalRecords->isEmpty()) {
            return;
        }

        foreach ($medicalRecords as $index => $record) {
            File::create([
                'fileable_type' => 'App\Models\MedicalRecord',
                'fileable_id'   => $record->id,
                'file_path'     => "dummy_lab_result_" . ($index + 1) . ".pdf",
                'file_name'     => "Hasil_Lab_Pemeriksaan_" . ($index + 1) . ".pdf",
                'file_type'     => 'application/pdf',
                'uploaded_by'   => $uploaderId,
            ]);
        }
    }
}