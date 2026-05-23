<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\API\FileResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Exception;

class FileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = File::query();

        if ($user?->role === 'doctor' && $user->doctor) {
            $doctorId = $user->doctor->id;
            $query->where('uploaded_by', $user->id)
                  ->orWhere(function ($q) use ($doctorId) {
                      $q->where('fileable_type', 'App\Models\Doctor')
                        ->where('fileable_id', $doctorId);
                  })
                  ->orWhere(function ($q) use ($doctorId) {
                      $q->where('fileable_type', 'App\Models\MedicalRecord')
                        ->whereHasMorph('fileable', ['App\Models\MedicalRecord'], function ($sub) use ($doctorId) {
                            $sub->where('doctor_id', $doctorId);
                        });
                  });
        } elseif ($user?->role === 'patient' && $user->patient) {
            $patientId = $user->patient->id;
            $query->where('uploaded_by', $user->id)
                  ->orWhere(function ($q) use ($patientId) {
                      $q->where('fileable_type', 'App\Models\Patient')
                        ->where('fileable_id', $patientId);
                  })
                  ->orWhere(function ($q) use ($patientId) {
                      $q->where('fileable_type', 'App\Models\MedicalRecord')
                        ->whereHasMorph('fileable', ['App\Models\MedicalRecord'], function ($sub) use ($patientId) {
                            $sub->whereHas('appointment', function ($ap) use ($patientId) {
                                $ap->where('patient_id', $patientId);
                            });
                        });
                  });
        } elseif ($user?->role !== 'admin') {
            $query->where('id', 0);
        }

        $files = $query->paginate(10);

        $data = [
            'items' => FileResource::collection($files),
            'pagination' => [
                'current_page' => $files->currentPage(),
                'last_page' => $files->lastPage(),
                'per_page' => $files->perPage(),
                'total' => $files->total(),
            ]
        ];

        return $this->sendResponse($data, 'Data file berhasil diambil');
    }

    public function store(UploadFileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $uploadedFile = $request->file('file');
            $filePath = Storage::disk('medical')->putFile('', $uploadedFile);

            $file = File::create([
                'fileable_type' => $validated['fileable_type'],
                'fileable_id' => $validated['fileable_id'],
                'file_path' => $filePath,
                'file_name' => $uploadedFile->getClientOriginalName(),
                'file_type' => $uploadedFile->getClientMimeType(),
                'uploaded_by' => $request->user()?->id
            ]);

            DB::commit();

            return $this->sendResponse(
                new FileResource($file),
                'File berhasil diunggah secara aman di penyimpanan privat.',
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($filePath) && Storage::disk('medical')->exists($filePath)) {
                Storage::disk('medical')->delete($filePath);
            }
            throw $e;
        }
    }

    public function show(Request $request, string|int $id): Response
    {
        $file = File::findOrFail($id);
        $user = $request->user();
        $isAuthorized = false;

        if ($user->role === 'admin') {
            $isAuthorized = true;
        } elseif ($user->role === 'doctor') {
            $doctorId = $user->doctor?->id;
            if ($file->fileable_type === 'App\Models\Doctor' && $file->fileable_id == $doctorId) {
                $isAuthorized = true;
            } elseif ($file->uploaded_by == $user->id) {
                $isAuthorized = true;
            } elseif ($file->fileable_type === 'App\Models\MedicalRecord' && $file->fileable?->doctor_id == $doctorId) {
                $isAuthorized = true;
            }
        } elseif ($user->role === 'patient') {
            $patientId = $user->patient?->id;
            if ($file->fileable_type === 'App\Models\Patient' && $file->fileable_id == $patientId) {
                $isAuthorized = true;
            } elseif ($file->uploaded_by == $user->id) {
                $isAuthorized = true;
            } elseif ($file->fileable_type === 'App\Models\MedicalRecord' && $file->fileable?->appointment?->patient_id == $patientId) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            throw new AccessDeniedHttpException('Anda tidak memiliki hak akses untuk mengunduh dokumen ini.');
        }

        $absolutePath = Storage::disk('medical')->path($file->file_path);

        if (!file_exists($absolutePath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fisik file tidak ditemukan di server.',
            ], 404);
        }

        return response()->download($absolutePath, $file->file_name);
    }

    public function update(UpdateFileRequest $request, string|int $id): JsonResponse
    {
        $file = File::findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $file->update($validated);
            DB::commit();

            return $this->sendResponse(
                new FileResource($file->refresh()),
                'Nama file berhasil diperbarui'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $file = File::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'admin' && $file->uploaded_by !== $user->id) {
            throw new AccessDeniedHttpException('Anda tidak memiliki izin untuk menghapus file ini');
        }

        DB::beginTransaction();
        try {
            $file->delete();
            DB::commit();

            return $this->sendResponse(
                null,
                'Data file berhasil ditandai sebagai dihapus (soft delete).'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}