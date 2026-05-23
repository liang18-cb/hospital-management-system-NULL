<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Patient;

class UpdatePatientRequest extends FormRequest
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

        if ($user->role === 'patient') {
            $patientId = $this->route('patient');
            $patient = Patient::find($patientId);
            
            return $patient && $user->id === $patient->user_id;
        }

        return false;
    }

    public function rules(): array
    {
        $patientId = $this->route('patient');
        $patient = Patient::find($patientId);
        $userId = $patient ? $patient->user_id : null;

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string',
            'date_of_birth' => 'sometimes|required|date',
            'password' => 'sometimes|required|string|min:8',
        ];
    }
}