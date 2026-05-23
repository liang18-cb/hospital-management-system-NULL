<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

class FileValidationTest extends TestCase
{
    public function test_file_extension_validation()
    {
        $file = UploadedFile::fake()->create('document.pdf');
        $validator = Validator::make(['file' => $file], ['file' => 'required|mimes:pdf']);
        $this->assertFalse($validator->fails());
    }
}