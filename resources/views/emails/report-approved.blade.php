
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Laporan Magang Disetujui</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #374151; background: #f9fafb; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 32px; text-align: center; }
        .header h1 { font-size: 24px; margin: 0; font-weight: 600; }
        .content { padding: 32px; }
        .success-badge { background: #dcfce7; color: #166534; padding: 8px 16px; border-radius: 9999px; font-weight: 500; }
        .footer { background: #f8fafc; padding: 24px; text-align: center; color: #6b7280; font-size: 14px; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Laporan Disetujui!</h1>
            <p style="opacity: 0.9; margin-top: 8px;">{{ $report_date }}</p>
        </div>
        
        <div class="content">
            <h2>Halo {{ $student_name }},</h2>
            
            <p>Laporan harian magang Anda untuk tanggal <strong>{{ $report_date_formatted }}</strong> telah disetujui oleh pembimbing:</p>
            
            <div style="margin: 24px 0;">
                <div class="success-badge" style="font-size: 16px; display: inline-block;">
                    <strong>{{ $supervisor_name }}</strong>
                </div>
            </div>
            
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #10b981;">
                <h3>📝 Ringkasan Laporan:</h3>
                <p><strong>Kegiatan:</strong> {{ $activity_summary }}</p>
                @if($feedback)
                    <p><strong>Catatan Pembimbing:</strong> {{ $feedback }}</p>
                @endif
            </div>
            
            <p style="margin-top: 24px;">
                Terus semangat menyelesaikan program magang Anda! Jika ada pertanyaan, hubungi pembimbing melalui dashboard.
            </p>
            
            <a href="{{ $dashboard_url }}" class="button">Buka Dashboard</a>
        </div>
        
        <div class="footer">
            <p>Ini adalah pesan otomatis dari <strong>Sistem Manajemen Magang</strong></p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

