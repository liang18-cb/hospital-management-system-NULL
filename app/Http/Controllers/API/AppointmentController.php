<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\API\AppointmentResource;
use App\Mail\AppointmentBooked;
use App\Mail\AppointmentStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Exception;

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

        $data = [
            'items' => AppointmentResource::collection($appointments),
            'pagination' => [
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
            ]
        ];

        return $this->sendResponse(
            $data,
            'Data janji temu berhasil diambil'
        );
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            if ($user->role === 'patient') {
                $validated['patient_id'] = $user->patient?->id;
            }

            $validated['status'] = 'pending';

            $appointment = Appointment::create($validated);
            DB::commit();

            $appointment->load(['doctor.user', 'patient.user', 'schedule']);

            if ($appointment->patient?->user?->email) {
                Mail::to($appointment->patient->user->email)->send(new AppointmentBooked($appointment));
            }

            return $this->sendResponse(
                new AppointmentResource($appointment),
                'Janji temu berhasil dibuat',
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $oldStatus = $appointment->status;

            $appointment->update($validated);
            DB::commit();

            $appointment->load(['doctor.user', 'patient.user', 'schedule'])->refresh();

            if ($oldStatus !== $appointment->status && in_array($appointment->status, ['confirmed', 'cancelled', 'completed'])) {
                if ($appointment->patient?->user?->email) {
                    Mail::to($appointment->patient->user->email)->send(new AppointmentStatusChanged($appointment));
                }
            }

            return $this->sendResponse(
                new AppointmentResource($appointment),
                'Data janji temu berhasil diperbarui'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $user = $request->user();
        $appointment = Appointment::findOrFail($id);

        if ($user?->role === 'doctor' && $appointment->doctor_id !== $user->doctor?->id) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }

        if ($user?->role === 'patient' && $appointment->patient_id !== $user->patient?->id) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }

        DB::beginTransaction();
        try {
            $appointment->delete();
            DB::commit();

            return $this->sendResponse(
                null,
                'Data janji temu berhasil dihapus'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}