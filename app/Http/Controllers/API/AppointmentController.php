<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Appointment::query();

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data janji temu berhasil diambil',
            'data' => $query->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
            'appointment_date' => 'required|date',
            'complaint' => 'nullable|string',
        ]);

        $validated['patient_id'] = $user->patient->id;
        $validated['status'] = 'pending';

        $appointment = Appointment::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Janji temu berhasil dibuat',
            'data' => $appointment
        ], 201);
    }

    public function show(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        if ($user->isDoctor() && $appointment->doctor_id !== $user->doctor->id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        if ($user->isPatient() && $appointment->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail janji temu berhasil ditemukan',
            'data' => $appointment
        ], 200);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'sometimes|integer|exists:patients,id',
            'doctor_id' => 'sometimes|integer|exists:doctors,id',
            'schedule_id' => 'sometimes|integer|exists:schedules,id',
            'appointment_date' => 'sometimes|date',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'complaint' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data janji temu berhasil diperbarui',
            'data' => $appointment
        ], 200);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data janji temu berhasil dihapus'
        ], 200);
    }
}