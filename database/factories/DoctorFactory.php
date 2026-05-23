<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{   
    protected $model = Doctor::class;

    public function definition(): array
    {
        $fakerId = fake('id_ID');
        $phone = $fakerId->unique()->numerify('+628##########');

        return [
            'user_id' => User::factory()->doctor(),
            'specialization' => fake()->randomElement([
                'Dokter Umum', 
                'Dokter Spesialis Jantung', 
                'Dokter Spesialis Anak', 
                'Dokter Bedah', 
                'Dokter Gigi'
            ]),
            'phone' => $phone,
            'photo' => null,
        ];
    }
}