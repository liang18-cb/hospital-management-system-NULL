<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Sesuai dengan entitas database minimal di dokumen proyek.
     */
    protected $fillable = [
    'user_id',
    'specialization',
    'phone',
    'address',
    'photo',
];

    /**
     * Relasi ke model User.
     * Setiap dokter terhubung ke satu akun user untuk kebutuhan login.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Appointment.
     * Satu dokter dapat memiliki banyak janji temu dengan pasien.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Relasi ke model Schedule.
     * Menghubungkan dokter dengan jadwal praktik mereka.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Relasi Polymorphic ke model File.
     * Digunakan untuk mengelola foto profil dokter menggunakan Laravel File Storage.
     */
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}