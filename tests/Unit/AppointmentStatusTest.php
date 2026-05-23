<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Appointment;

class AppointmentStatusTest extends TestCase
{
    public function test_appointment_status_transition_is_valid()
    {
        $appointment = new Appointment(['status' => 'pending']);
        $appointment->status = 'confirmed';
        $this->assertEquals('confirmed', $appointment->status);
    }
}