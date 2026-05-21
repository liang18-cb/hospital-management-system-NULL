<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class ReportController extends BaseController
{
    public function indexAppointments()
    {
        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('users as patient_users', 'patients.user_id', '=', 'patient_users.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('users as doctor_users', 'doctors.user_id', '=', 'doctor_users.id')
            ->select(
                'appointments.id as appointment_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.complaint',
                'patient_users.name as patient_name',
                'doctor_users.name as doctor_name',
                'doctors.specialization'
            )
            ->get();

        return response()->json($appointments);
    }

    public function indexDoctorSchedules()
    {
        $doctorSchedules = DB::table('doctors')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->leftJoin('schedules', 'doctors.id', '=', 'schedules.doctor_id')
            ->select(
                'users.name as doctor_name',
                'doctors.specialization',
                'schedules.day_of_week',
                'schedules.start_time',
                'schedules.end_time'
            )
            ->get();

        return response()->json($doctorSchedules);
    }

    public function indexMedicalRecordsReport()
    {
        $medicalRecordsReport = DB::table('medical_records')
            ->join('appointments', 'medical_records.appointment_id', '=', 'appointments.id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('users as patient_users', 'patients.user_id', '=', 'patient_users.id')
            ->join('doctors', 'medical_records.doctor_id', '=', 'doctors.id')
            ->join('users as doctor_users', 'doctors.user_id', '=', 'doctor_users.id')
            ->select(
                'medical_records.id as record_id',
                'medical_records.diagnosis',
                'medical_records.prescription',
                'medical_records.created_at as treatment_date',
                'patient_users.name as patient_name',
                'patients.date_of_birth',
                'doctor_users.name as doctor_name',
                'doctors.specialization'
            )
            ->orderBy('medical_records.created_at', 'desc')
            ->get();

        return response()->json($medicalRecordsReport);
    }
}