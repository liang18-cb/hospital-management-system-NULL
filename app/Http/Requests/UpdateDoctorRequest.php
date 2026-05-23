<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Doctor;

class UpdateDoctorRequest extends FormRequest
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
            $doctorId = $this->route('doctor');
            $doctor = Doctor::find($doctorId);
            
            return $doctor && $user->id === $doctor->user_id;
        }

        return false;
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
            'photo' => 'sometimes|nullable|string',
        ];
    }
}