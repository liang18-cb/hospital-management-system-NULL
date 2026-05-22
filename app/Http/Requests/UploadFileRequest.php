<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UploadFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->hasFile('file')) {
            $originalName = $this->file('file')->getClientOriginalName();
            
            if (preg_match('/\.\.\/|\.\.\\\\/', $originalName) || str_contains($originalName, '..')) {
                throw ValidationException::withMessages([
                    'file' => ['Unggahan ditolak. Nama berkas terdeteksi mengandung indikasi ancaman path traversal.']
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
            'fileable_type' => 'required|string',
            'fileable_id' => 'required|integer',
        ];
    }
}