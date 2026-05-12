<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Admin User
        |--------------------------------------------------------------------------
        | Digunakan untuk login admin di Postman / frontend
        |--------------------------------------------------------------------------
        */
        User::firstOrCreate(
            [
                'email' => 'admin@klinik.com',
            ],
            [
                'name' => 'Admin Klinik',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Dokter
        |--------------------------------------------------------------------------
        | Membuat 5 user dokter + data dokter
        |--------------------------------------------------------------------------
        */
        User::factory(5)
            ->create([
                'role' => 'doctor',
            ])
            ->each(function ($user) {
                Doctor::factory()->create([
                    'user_id' => $user->id,
                ]);
            });

        /*
        |--------------------------------------------------------------------------
        | 3. Pasien
        |--------------------------------------------------------------------------
        | Membuat 10 user pasien + data pasien
        |--------------------------------------------------------------------------
        */
        User::factory(10)
            ->create([
                'role' => 'user',
            ])
            ->each(function ($user) {
                Patient::factory()->create([
                    'user_id' => $user->id,
                ]);
            });
    }
}