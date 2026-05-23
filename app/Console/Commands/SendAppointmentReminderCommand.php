<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAppointmentReminderCommand extends Command
{
    protected $signature = 'appointment:send-reminder';
    protected $description = 'Kirim email pengingat H-1 kepada pasien dengan status confirmed';

    public function handle(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $appointments = Appointment::with(['patient.user', 'doctor.user', 'schedule'])
            ->whereDate('appointment_date', $tomorrow)
            ->where('status', 'confirmed')
            ->get();

        foreach ($appointments as $appointment) {
            if ($appointment->patient?->user?->email) {
                Mail::to($appointment->patient->user->email)->send(new AppointmentReminder($appointment));
            }
        }

        $this->info('Email pengingat H-1 berhasil dikirim.');
    }
}