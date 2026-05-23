<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\File;
use App\Models\Doctor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_file()
    {
        Storage::fake('medical');
        
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'doctor']);
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $user = $user->fresh();
        
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/files/upload', [
            'file' => UploadedFile::fake()->create('document.pdf', 500),
            'fileable_type' => 'App\Models\Doctor',
            'fileable_id' => $doctor->id
        ]);

        $response->assertStatus(201);
    }

    public function test_user_can_download_file()
    {
        Storage::fake('medical');
        Storage::disk('medical')->put('test.pdf', 'dummy content');
        
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'doctor']);
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $user = $user->fresh();
        
        $file = File::create([
            'file_name' => 'test.pdf',
            'file_path' => 'test.pdf',
            'file_type' => 'application/pdf',
            'fileable_type' => 'App\Models\Doctor',
            'fileable_id' => $doctor->id,
            'uploaded_by' => (int) $user->id
        ]);

        $this->actingAs($user);
        
        $response = $this->getJson('/api/v1/files/' . $file->id);
        $response->assertStatus(200);
    }

    public function test_user_can_delete_file()
    {
        Storage::fake('medical');
        
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'doctor']);
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $user = $user->fresh();
        
        $file = File::create([
            'file_name' => 'test.pdf',
            'file_path' => 'files/test.pdf',
            'file_type' => 'application/pdf',
            'fileable_type' => 'App\Models\Doctor',
            'fileable_id' => $doctor->id,
            'uploaded_by' => (int) $user->id
        ]);

        $this->assertEquals((int)$user->id, (int)$file->uploaded_by, "uploaded_by di database tidak sesuai dengan user ID");

        $this->actingAs($user);

        $response = $this->deleteJson('/api/v1/files/' . $file->id);
        $response->assertStatus(200);
    }
}