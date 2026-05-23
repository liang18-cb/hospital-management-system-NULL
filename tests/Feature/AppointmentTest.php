<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_create_appointment()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create();
        
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/appointments', [
            'doctor_id' => $doctor->id,
            'appointment_date' => '2026-06-01',
            'complaint' => 'Sakit kepala'
        ]);

        $response->assertStatus(201);
    }

}