<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'specialization' => $this->specialization,
            'phone' => $this->phone,
            'role' => $this->user->role,
            'photo_url' => $this->photo
                ? asset('storage/' . $this->photo)
                : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}