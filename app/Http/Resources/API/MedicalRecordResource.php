<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'diagnosis' => $this->diagnosis,
            'prescription' => $this->prescription,
            'notes' => $this->notes,
            'doctor' => new DoctorResource($this->whenLoaded('doctor') ?? $this->doctor),
            'patient' => new PatientResource($this->relationLoaded('appointment') ? $this->appointment->patient : $this->appointment?->patient),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}