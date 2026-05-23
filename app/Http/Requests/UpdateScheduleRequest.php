<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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

        if ($user->role === 'doctor') {
            $scheduleId = $this->route('schedule');
            $schedule = Schedule::find($scheduleId);
            return $schedule && $user->doctor?->id === $schedule->doctor_id;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'sometimes|required|exists:doctors,id',
            'day_of_week' => 'sometimes|required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
        ];
    }
}