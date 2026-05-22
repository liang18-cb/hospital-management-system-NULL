<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Http\Resources\API\MedicalRecordResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Exception;

class MedicalRecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = MedicalRecord::with(['doctor.user', 'appointment.patient.user']);

        if ($user?->role === 'doctor' && $user->doctor) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user?->role === 'patient' && $user->patient) {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('patient_id', $user->patient->id);
            });
        }

        $medicalRecords = $query->paginate(10);

        return $this->sendResponse(
            MedicalRecordResource::collection($medicalRecords),
            'Data rekam medis berhasil diambil'
        );
    }

    public function store(StoreMedicalRecordRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $appointment = Appointment::findOrFail($validated['appointment_id']);

        if ($appointment->doctor_id !== $user->doctor?->id) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }

        $validated['doctor_id'] = $user->doctor->id;

        DB::beginTransaction();
        try {
            $medicalRecord = MedicalRecord::create($validated);
            DB::commit();

            return $this->sendResponse(
                new MedicalRecordResource($medicalRecord->load(['doctor.user', 'appointment.patient.user'])),
                'Rekam medis baru berhasil ditambahkan',
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
        $medicalRecord = MedicalRecord::with(['doctor.user', 'appointment.patient.user'])->findOrFail($id);

        if ($user?->role === 'doctor' && $medicalRecord->doctor_id !== $user->doctor?->id) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }

        if ($user?->role === 'patient') {
            $appointment = $medicalRecord->appointment;
            if (!$appointment || $appointment->patient_id !== $user->patient?->id) {
                throw new AccessDeniedHttpException('Unauthorized access.');
            }
        }

        return $this->sendResponse(
            new MedicalRecordResource($medicalRecord),
            'Detail rekam medis berhasil ditemukan'
        );
    }

    public function update(UpdateMedicalRecordRequest $request, string|int $id): JsonResponse
    {
        $medicalRecord = MedicalRecord::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $medicalRecord->update($request->validated());
            DB::commit();

            return $this->sendResponse(
                new MedicalRecordResource($medicalRecord->load(['doctor.user', 'appointment.patient.user'])->refresh()),
                'Data rekam medis berhasil diperbarui'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(string|int $id): JsonResponse
    {
        $medicalRecord = MedicalRecord::findOrFail($id);

        DB::beginTransaction();
        try {
            $medicalRecord->delete();
            DB::commit();

            return $this->sendResponse(
                null,
                'Data rekam medis berhasil dihapus'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}