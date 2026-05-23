<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Booking</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; padding: 20px;">
    <h2>Halo, {{ $appointment->patient->user->name ?? 'Pasien' }}!</h2>
    <p>Terima kasih telah melakukan pendaftaran janji temu. Berikut adalah rincian pesanan Anda:</p>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="padding: 5px; font-weight: bold; width: 150px;">Dokter</td>
            <td style="padding: 5px;">: {{ $appointment->doctor->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 5px; font-weight: bold;">Tanggal</td>
            <td style="padding: 5px;">: {{ $appointment->appointment_date }}</td>
        </tr>
        <tr>
            <td style="padding: 5px; font-weight: bold;">Jam</td>
            <td style="padding: 5px;">: {{ $appointment->schedule->start_time ?? '-' }} WIB</td>
        </tr>
        <tr>
            <td style="padding: 5px; font-weight: bold;">Nomor Antrian</td>
            <td style="padding: 5px; font-size: 16px; color: #0284c7; font-weight: bold;">: {{ $appointment->queue_number ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 5px; font-weight: bold;">Keluhan</td>
            <td style="padding: 5px;">: {{ $appointment->complaint }}</td>
        </tr>
    </table>
    <p style="margin-top: 20px;">Silakan datang 15 menit sebelum jadwal yang ditentukan. Terima kasih.</p>
</body>
</html>