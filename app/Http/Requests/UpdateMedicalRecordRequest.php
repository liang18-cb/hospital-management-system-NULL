<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'doctor';
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'sometimes|required|integer|exists:appointments,id',
            'diagnosis' => 'sometimes|required|string',
            'treatment' => 'sometimes|required|string',
            'notes' => 'nullable|string',
        ];
    }
}