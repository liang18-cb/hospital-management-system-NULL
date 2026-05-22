<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Resources\API\DoctorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class DoctorController extends Controller
{
    public function index(): JsonResponse
    {
        $doctors = Doctor::with('user')->paginate(10);

        return $this->sendResponse(
            DoctorResource::collection($doctors),
            'Data dokter berhasil diambil'
        );
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'doctor',
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialization' => $validated['specialization'],
                'phone' => $validated['phone'],
            ]);

            DB::commit();

            return $this->sendResponse(
                new DoctorResource($doctor->load('user')),
                'Data dokter berhasil ditambahkan',
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(string|int $id): JsonResponse
    {
        $doctor = Doctor::with('user')->findOrFail($id);

        return $this->sendResponse(
            new DoctorResource($doctor),
            'Detail dokter berhasil ditemukan'
        );
    }

    public function update(UpdateDoctorRequest $request, string|int $id): JsonResponse
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $userFields = [];
            if (isset($validated['name'])) $userFields['name'] = $validated['name'];
            if (isset($validated['email'])) $userFields['email'] = $validated['email'];
            if (isset($validated['password'])) $userFields['password'] = Hash::make($validated['password']);

            if (!empty($userFields)) {
                $doctor->user->update($userFields);
            }

            $doctorFields = [];
            if (isset($validated['specialization'])) $doctorFields['specialization'] = $validated['specialization'];
            if (isset($validated['phone'])) $doctorFields['phone'] = $validated['phone'];

            if (!empty($doctorFields)) {
                $doctor->update($doctorFields);
            }

            DB::commit();

            return $this->sendResponse(
                new DoctorResource($doctor->refresh()),
                'Data dokter berhasil diperbarui'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(string|int $id): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $doctor->delete();
            $doctor->user?->delete();
            
            DB::commit();

            return $this->sendResponse(
                null,
                'Data dokter berhasil dihapus'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}