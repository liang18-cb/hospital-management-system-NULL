<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Http\Resources\API\PatientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    // GET all patients
    public function index()
    {
        $patients = Patient::with('user')->get();
        return PatientResource::collection($patients);
    }

    // POST create patient + user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'date_of_birth' => 'required|date',
            'address'       => 'required|string',
            'phone'         => 'required|string',
        ]);

        // 1. Create user (akun login)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 2. Create patient (profil medis)
        $patient = Patient::create([
            'user_id' => $user->id,
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
        ]);

        return new PatientResource($patient);
    }

    // GET detail patient
    public function show($id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        return new PatientResource($patient);
    }

    // UPDATE patient
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'date_of_birth' => 'sometimes|date',
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string',
        ]);

        $patient->update($validated);

        return new PatientResource($patient);
    }

    // DELETE patient
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return response()->json([
            'message' => 'Patient deleted successfully'
        ]);
    }
}