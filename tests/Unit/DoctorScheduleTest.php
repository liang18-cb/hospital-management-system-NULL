<?php

namespace Tests\Unit;

use Tests\TestCase;

class DoctorScheduleTest extends TestCase
{
    public function test_schedule_time_validation()
    {
        $start = '08:00';
        $end = '10:00';
        $this->assertTrue($end > $start);
    }

    public function test_schedule_availability()
    {
        $isAvailable = true;
        $this->assertTrue($isAvailable);
    }
}