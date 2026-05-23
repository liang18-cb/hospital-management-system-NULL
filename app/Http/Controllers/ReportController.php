<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function export(Request $request): JsonResponse|StreamedResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:csv',
            'report' => 'required|string|in:appointments',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(
                null,
                'Parameter pencarian laporan tidak valid.',
                422,
                $validator->errors()
            );
        }

        $type = $request->query('type', 'csv');
        $report = $request->query('report');

        if ($type === 'csv' && $report === 'appointments') {
            return $this->streamCsv($request);
        }

        return $this->sendResponse(
            ['supported_formats' => ['csv'], 'supported_reports' => ['appointments']],
            'Format atau jenis laporan tidak didukung.',
            400
        );
    }

    private function streamCsv(Request $request): StreamedResponse
    {
        $report = $request->query('report');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $filename = "laporan_{$report}_" . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, must-revalidate',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID', 'Nama Pasien', 'Nama Dokter', 'Tanggal Janji Temu', 'Status', 'Keluhan Utama']);

            $query = Appointment::with(['patient.user', 'doctor.user']);

            if ($startDate) {
                $query->whereDate('appointment_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('appointment_date', '<=', $endDate);
            }

            $query->chunk(200, function ($appointments) use ($file) {
                foreach ($appointments as $app) {
                    fputcsv($file, [
                        $app->id,
                        $app->patient->user->name ?? '-',
                        $app->doctor->user->name ?? '-',
                        $app->appointment_date,
                        $app->status,
                        $app->complaint,
                    ]);
                }
            });

            fclose($file);
        }, 200, $headers);
    }
}