<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data dokter berhasil diambil',
            'data' => $doctors
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:doctors,email',
        ]);

        $doctor = Doctor::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data dokter berhasil ditambahkan',
            'data' => $doctor
        ], 201);
    }

    public function show(int $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data dokter tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail dokter berhasil ditemukan',
            'data' => $doctor
        ], 200);
    }

    public function update(Request $request, int $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data dokter tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'specialization' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:doctors,email,' . $id,
        ]);

        $doctor->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data dokter berhasil diperbarui',
            'data' => $doctor
        ], 200);
    }

    public function destroy(int $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data dokter tidak ditemukan'
            ], 404);
        }

        $doctor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data dokter berhasil dihapus'
        ], 200);
    }
}