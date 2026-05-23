<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\File;

class UpdateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $fileId = $this->route('file');
        $file = File::find($fileId);

        if (!$file) {
            return false;
        }

        return $file->uploaded_by === $user->id;
    }

    public function rules(): array
    {
        return [
            'file_name' => 'required|string|max:255',
        ];
    }
}