<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Http\Resources\API\DoctorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    /**
     * Menampilkan semua dokter
     */
    public function index()
    {
        $doctors = Doctor::with('user')->get();

        return DoctorResource::collection($doctors);
    }

    /**
     * Menampilkan satu dokter
     */
    public function show($id)
    {
        $doctor = Doctor::with('user')->find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Dokter tidak ditemukan'
            ], 404);
        }

        return new DoctorResource($doctor);
    }

    /**
     * Menambahkan dokter baru
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'specialization' => 'required|string',
        'phone' => 'required|string',
        'address' => 'required|string',
    ]);

    // Buat user
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'doctor',
    ]);

    // Buat doctor
    $doctor = Doctor::create([
        'user_id' => $user->id,
        'specialization' => $validated['specialization'],
        'phone' => $validated['phone'],
        'address' => $validated['address'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Doctor berhasil dibuat',
        'data' => new DoctorResource($doctor->load('user'))
    ], 201);
}

    /**
     * Update dokter
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::with('user')->find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Dokter tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'specialization' => 'required|string',
            'phone' => 'required|string',
        ]);

        $doctor->update([
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Doctor berhasil diupdate',
            'data' => new DoctorResource($doctor->load('user'))
        ]);
    }

    /**
     * Hapus dokter
     */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Dokter tidak ditemukan'
            ], 404);
        }

        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor berhasil dihapus'
        ]);
    }
}