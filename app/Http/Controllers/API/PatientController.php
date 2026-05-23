<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\API\PatientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class PatientController extends Controller
{
    public function index(): JsonResponse
    {
        $patients = Patient::with('user')->paginate(10);

        $data = [
            'items' => PatientResource::collection($patients),
            'pagination' => [
                'current_page' => $patients->currentPage(),
                'last_page' => $patients->lastPage(),
                'per_page' => $patients->perPage(),
                'total' => $patients->total(),
            ]
        ];

        return $this->sendResponse(
            $data,
            'Data pasien berhasil diambil'
        );
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'patient',
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'date_of_birth' => $validated['date_of_birth'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
            ]);

            DB::commit();

            return $this->sendResponse(
                new PatientResource($patient->load('user')),
                'Data pasien berhasil ditambahkan',
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(string|int $id): JsonResponse
    {
        $patient = Patient::with('user')->findOrFail($id);

        return $this->sendResponse(
            new PatientResource($patient),
            'Detail pasien berhasil ditemukan'
        );
    }

    public function update(UpdatePatientRequest $request, string|int $id): JsonResponse
    {
        $patient = Patient::with('user')->findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $userFields = [];
            if (isset($validated['name'])) $userFields['name'] = $validated['name'];
            if (isset($validated['email'])) $userFields['email'] = $validated['email'];
            if (isset($validated['password'])) $userFields['password'] = Hash::make($validated['password']);

            if (!empty($userFields) && $patient->user) {
                $patient->user->update($userFields);
            }

            $patientFields = [];
            if (isset($validated['date_of_birth'])) $patientFields['date_of_birth'] = $validated['date_of_birth'];
            if (isset($validated['address'])) $patientFields['address'] = $validated['address'];
            if (isset($validated['phone'])) $patientFields['phone'] = $validated['phone'];

            if (!empty($patientFields)) {
                $patient->update($patientFields);
            }

            DB::commit();

            return $this->sendResponse(
                new PatientResource($patient->refresh()),
                'Data pasien berhasil diperbarui'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(string|int $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($patient->user) {
                $patient->user->delete();
            } else {
                $patient->delete();
            }

            DB::commit();

            return $this->sendResponse(
                null,
                'Data pasien berhasil dihapus'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}