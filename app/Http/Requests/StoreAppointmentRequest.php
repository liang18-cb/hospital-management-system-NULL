<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->role === 'patient' || $user->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|exists:doctors,id',
            'schedule_id' => 'required|exists:schedules,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'complaint' => 'required|string',
            'patient_id' => 'required_if:user_role,admin|exists:patients,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()) {
            $this->merge([
                'user_role' => $this->user()->role,
            ]);
        }
    }
}