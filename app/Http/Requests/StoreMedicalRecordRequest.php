<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->role === 'doctor' || $user->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'diagnosis' => 'required|string',
            'prescription' => 'required|string',
            'notes' => 'nullable|string',
        ];
    }
}