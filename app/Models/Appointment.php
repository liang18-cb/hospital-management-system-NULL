<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * Mencakup relasi ke pasien, dokter, jadwal, dan keluhan (complaint)[cite: 25].
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'schedule_id',
        'appointment_date',
        'status', // pending, confirmed, completed, cancelled [cite: 104]
        'complaint',
    ];

    /**
     * Relasi ke model Patient.
     * Mengetahui pasien mana yang memiliki janji temu ini[cite: 25].
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relasi ke model Doctor.
     * Mengetahui dokter mana yang dituju[cite: 25].
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relasi ke model MedicalRecord.
     * Satu janji temu biasanya menghasilkan satu rekam medis[cite: 26].
     */
    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}