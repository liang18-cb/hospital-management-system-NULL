<?php

namespace App\Http\Controllers\API;

use App\Models\MedicalRecord;
use App\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = MedicalRecord::query();

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->isPatient()) {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('patient_id', $user->patient->id);
            });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data rekam medis berhasil diambil',
            'data' => $query->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
            'diagnosis' => 'required|string',
            'prescription' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($validated['appointment_id']);

        if ($appointment->doctor_id !== $user->doctor->id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $validated['doctor_id'] = $user->doctor->id;

        $medicalRecord = MedicalRecord::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Rekam medis baru berhasil ditambahkan',
            'data' => $medicalRecord
        ], 201);
    }

    public function show(Request $request, MedicalRecord $medicalRecord)
    {
        $user = $request->user();

        if ($user->isDoctor() && $medicalRecord->doctor_id !== $user->doctor->id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        if ($user->isPatient()) {
            $appointment = $medicalRecord->appointment;
            if (!$appointment || $appointment->patient_id !== $user->patient->id) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail rekam medis berhasil ditemukan',
            'data' => $medicalRecord
        ], 200);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'appointment_id' => 'sometimes|integer|exists:appointments,id',
            'doctor_id' => 'sometimes|integer|exists:doctors,id',
            'diagnosis' => 'sometimes|string',
            'prescription' => 'sometimes|string',
            'notes' => 'nullable|string'
        ]);

        $medicalRecord->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data rekam medis berhasil diperbarui',
            'data' => $medicalRecord
        ], 200);
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data rekam medis berhasil dihapus'
        ], 200);
    }
}