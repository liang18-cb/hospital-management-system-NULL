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
use Exception;

class FileController extends Controller
{
    public function index(): JsonResponse
    {
        $files = File::paginate(10);

        return $this->sendResponse(
            FileResource::collection($files),
            'Data file berhasil diambil'
        );
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
            if ($file->fileable_type === 'App\Models\Doctor' && $file->fileable_id == $user->id) {
                $isAuthorized = true;
            } elseif ($file->fileable_type === 'App\Models\MedicalRecord' || $file->uploaded_by == $user->id) {
                $isAuthorized = true;
            }
        } elseif ($user->role === 'patient') {
            if ($file->fileable_type === 'App\Models\Patient' && $file->fileable_id == $user->id) {
                $isAuthorized = true;
            } elseif ($file->fileable_type === 'App\Models\MedicalRecord' && $file->fileable?->patient_id == $user->id) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk mengunduh dokumen ini.',
            ], 403);
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
        $file->update($request->validated());

        return $this->sendResponse(
            new FileResource($file->refresh()),
            'Nama file berhasil diperbarui'
        );
    }

    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $file = File::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'admin' && $file->uploaded_by !== $user->id) {
            return $this->sendResponse(null, 'Anda tidak memiliki izin untuk menghapus file ini', 403);
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