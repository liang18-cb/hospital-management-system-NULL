<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel appointments (Wajib)
            // Menggunakan onDelete('cascade') agar jika janji temu dihapus, rekam medisnya ikut terhapus
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            
            // Kolom untuk menyimpan hasil pemeriksaan 
            $table->text('diagnosis');      // Hasil diagnosis dokter
            $table->text('prescription');   // Resep obat
            $table->text('notes')->nullable(); // Catatan tambahan (opsional)

            // Menambahkan doctor_id sesuai entitas database minimal di dokumen 
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};