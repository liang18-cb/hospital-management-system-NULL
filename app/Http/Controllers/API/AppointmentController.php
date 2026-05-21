<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data janji temu berhasil diambil',
            'data' => $appointments
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'doctor_id' => 'required|integer',
            'schedule_id' => 'required|integer',
            'appointment_date' => 'required|date',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'complaint' => 'nullable|string',
        ]);

        $appointment = Appointment::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Janji temu berhasil dibuat',
            'data' => $appointment
        ], 201);
    }

    public function show(Appointment $appointment)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Detail janji temu berhasil ditemukan',
            'data' => $appointment
        ], 200);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'sometimes|integer',
            'doctor_id' => 'sometimes|integer',
            'schedule_id' => 'sometimes|integer',
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