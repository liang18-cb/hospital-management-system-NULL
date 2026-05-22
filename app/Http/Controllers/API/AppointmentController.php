<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\API\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Appointment::with(['doctor.user', 'patient.user', 'schedule']);

        if ($user?->role === 'doctor' && $user->doctor) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user?->role === 'patient' && $user->patient) {
            $query->where('patient_id', $user->patient->id);
        }

        $appointments = $query->paginate(10);

        return $this->sendResponse(
            AppointmentResource::collection($appointments),
            'Data janji temu berhasil diambil'
        );
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $validated['patient_id'] = $user?->patient?->id;
        $validated['status'] = 'pending';

        $appointment = Appointment::create($validated);

        return $this->sendResponse(
            new AppointmentResource($appointment->load(['doctor.user', 'patient.user', 'schedule'])),
            'Janji temu berhasil dibuat',
            201
        );
    }

    public function show(Request $request, string|int $id): JsonResponse
    {
        $user = $request->user();
        $appointment = Appointment::with(['doctor.user', 'patient.user', 'schedule'])->findOrFail($id);

        if ($user?->role === 'doctor' && $appointment->doctor_id !== $user->doctor?->id) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }

        if ($user?->role === 'patient' && $appointment->patient_id !== $user->patient?->id) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }

        return $this->sendResponse(
            new AppointmentResource($appointment),
            'Detail janji temu berhasil ditemukan'
        );
    }

    public function update(UpdateAppointmentRequest $request, string|int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->validated());

        return $this->sendResponse(
            new AppointmentResource($appointment->load(['doctor.user', 'patient.user', 'schedule'])->refresh()),
            'Data janji temu berhasil diperbarui'
        );
    }

    public function destroy(string|int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return $this->sendResponse(
            null,
            'Data janji temu berhasil dihapus'
        );
    }
}