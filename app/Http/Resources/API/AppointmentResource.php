<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointment_date' => $this->appointment_date,
            'status' => $this->status,
            'complaint' => $this->complaint,
            'doctor' => new DoctorResource($this->whenLoaded('doctor') ?? $this->doctor),
            'patient' => new PatientResource($this->whenLoaded('patient') ?? $this->patient),
            'schedule' => new ScheduleResource($this->whenLoaded('schedule') ?? $this->schedule),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}