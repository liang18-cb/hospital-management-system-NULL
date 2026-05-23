<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perubahan Status Janji Temu</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; padding: 20px;">
    <h2>Halo, {{ $appointment->patient->user->name ?? 'Pasien' }}!</h2>
    <p>Informasi janji temu Anda telah diperbarui oleh pihak klinik. Status janji temu Anda saat ini adalah:</p>
    <div style="display: inline-block; padding: 10px 20px; background-color: #f0fdf4; color: #166534; font-weight: bold; border-radius: 5px; margin: 10px 0; text-transform: uppercase;">
        {{ $appointment->status }}
    </div>
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
    </table>
    <p style="margin-top: 20px;">Terima kasih atas perhatian Anda.</p>
</body>
</html>