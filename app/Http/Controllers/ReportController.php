<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function export(Request $request): JsonResponse|StreamedResponse
    {
        $type = $request->query('type', 'csv');
        $report = $request->query('report', 'appointments');

        if ($type === 'csv') {
            return $this->streamCsv($report);
        }

        return $this->sendResponse(
            ['supported_formats' => ['csv']], 
            'Format laporan tidak valid', 
            400
        );
    }

    private function streamCsv(string $report): StreamedResponse
    {
        $filename = "laporan_{$report}_" . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($report) {
            $file = fopen('php://output', 'w');

            if ($report === 'appointments') {
                fputcsv($file, ['ID', 'Nama Pasien', 'Nama Dokter', 'Tanggal', 'Status', 'Keluhan']);
                Appointment::with(['patient.user', 'doctor.user'])->chunk(100, function ($appointments) use ($file) {
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
            }

            fclose($file);
        }, 200, $headers);
    }
}