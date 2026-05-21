<?php

namespace App\Http\Controllers\API;

use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function index()
    {
        $files = File::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data file berhasil diambil',
            'data' => $files
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fileable_type' => 'required|string',
            'fileable_id' => 'required|integer',
            'document' => 'required|file|max:2048'
        ]);

        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $originalName = $uploadedFile->getClientOriginalName();
            $mimeType = $uploadedFile->getClientMimeType();
            $size = $uploadedFile->getSize();
            
            $filePath = $uploadedFile->store('uploads', 'public');

            $file = File::create([
                'fileable_type' => $request->fileable_type,
                'fileable_id' => $request->fileable_id,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size,
                'uploaded_by' => Auth::user() ? Auth::user()->id : null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File berhasil diunggah',
                'data' => $file
            ], 201);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengunggah file'
        ], 400);
    }

    public function show(File $file)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Detail info file berhasil ditemukan',
            'data' => $file
        ], 200);
    }

    public function update(Request $request, File $file)
    {
        $request->validate([
            'original_name' => 'sometimes|string'
        ]);

        $file->update($request->only('original_name'));

        return response()->json([
            'status' => 'success',
            'message' => 'Nama file berhasil diperbarui',
            'data' => $file
        ], 200);
    }

    public function destroy(File $file)
    {
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data file berhasil dihapus'
        ], 200);
    }
}