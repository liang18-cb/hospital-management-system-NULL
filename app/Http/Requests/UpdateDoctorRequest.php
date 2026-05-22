<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Doctor;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin' || $this->user()?->role === 'doctor';
    }

    public function rules(): array
    {
        $doctorId = $this->route('doctor');
        $doctor = Doctor::find($doctorId);
        $userId = $doctor ? $doctor->user_id : null;

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'sometimes|required|string|min:8',
            'specialization' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
        ];
    }
}