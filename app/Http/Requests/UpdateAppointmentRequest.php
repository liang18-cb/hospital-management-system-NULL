<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $appointmentId = $this->route('appointment');
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            return false;
        }

        if ($user->role === 'doctor') {
            return $user->doctor?->id === $appointment->doctor_id;
        }

        if ($user->role === 'patient') {
            return $user->patient?->id === $appointment->patient_id;
        }

        return false;
    }

    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'appointment_date' => 'sometimes|required|date|after_or_equal:today',
            'complaint' => 'sometimes|required|string',
            'schedule_id' => 'sometimes|required|exists:schedules,id',
        ];

        if ($user && ($user->role === 'admin' || $user->role === 'doctor')) {
            $rules['status'] = 'sometimes|required|string|in:pending,confirmed,completed,cancelled';
        } else {
            $rules['status'] = 'sometimes|required|string|in:cancelled';
        }

        return $rules;
    }
}