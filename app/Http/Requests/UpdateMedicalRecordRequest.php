<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MedicalRecord;

class UpdateMedicalRecordRequest extends FormRequest
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

        $recordId = $this->route('medical_record');
        $medicalRecord = MedicalRecord::find($recordId);

        if (!$medicalRecord) {
            return false;
        }

        return $user->role === 'doctor' && $user->doctor?->id === $medicalRecord->doctor_id;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'sometimes|required|exists:appointments,id',
            'diagnosis' => 'sometimes|required|string',
            'prescription' => 'sometimes|required|string',
            'notes' => 'nullable|string',
        ];
    }
}