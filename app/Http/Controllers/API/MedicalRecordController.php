<?php

namespace App\Http\Controllers\API;

use App\Models\MedicalRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $medicalRecords = MedicalRecord::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data rekam medis berhasil diambil',
            'data' => $medicalRecords
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|integer',
            'doctor_id' => 'required|integer',
            'diagnosis' => 'required|string',
            'prescription' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $medicalRecord = MedicalRecord::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Rekam medis baru berhasil ditambahkan',
            'data' => $medicalRecord
        ], 201);
    }

    public function show(MedicalRecord $medicalRecord)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Detail rekam medis berhasil ditemukan',
            'data' => $medicalRecord
        ], 200);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'appointment_id' => 'sometimes|integer',
            'doctor_id' => 'sometimes|integer',
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