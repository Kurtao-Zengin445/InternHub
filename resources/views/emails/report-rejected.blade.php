
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Laporan Magang Perlu Revisi</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #374151; background: #f9fafb; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 32px; text-align: center; }
        .header h1 { font-size: 24px; margin: 0; font-weight: 600; }
        .content { padding: 32px; }
        .warning-badge { background: #fef3c7; color: #92400e; padding: 8px 16px; border-radius: 9999px; font-weight: 500; }
        .footer { background: #f8fafc; padding: 24px; text-align: center; color: #6b7280; font-size: 14px; }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Laporan Perlu Revisi</h1>
            <p style="opacity: 0.9; margin-top: 8px;">{{ $report_date }}</p>
        </div>
        
        <div class="content">
            <h2>Halo {{ $student_name }},</h2>
            
            <p>Laporan harian magang Anda untuk tanggal <strong>{{ $report_date_formatted }}</strong> perlu revisi dari pembimbing:</p>
            
            <div style="margin: 24px 0;">
                <div class="warning-badge" style="font-size: 16px; display: inline-block;">
                    <strong>{{ $supervisor_name }}</strong>
                </div>
            </div>
            
            <div style="background: #fef3c7; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <h3>📝 Feedback Revisi:</h3>
                <p>{{ $feedback }}</p>
            </div>
            
            <p style="margin-top: 24px;">
                Silakan edit laporan dan kirim ulang melalui dashboard. Terima kasih atas perhatiannya!
            </p>
            
            <a href="{{ $dashboard_url }}" class="button">Edit Laporan</a>
        </div>
        
        <div class="footer">
            <p>Ini adalah pesan otomatis dari <strong>Sistem Manajemen Magang</strong></p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

