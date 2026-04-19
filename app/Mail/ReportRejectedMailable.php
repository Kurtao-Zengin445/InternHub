
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportRejectedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $student_name,
        public string $supervisor_name,
        public string $report_date,
        public string $report_date_formatted,
        public string $feedback,
        public string $dashboard_url
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Magang Perlu Revisi ⚠️',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-rejected',
        );
    }
}


